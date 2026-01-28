<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Module;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * ModuleController - Manage modules (konten pembelajaran)
 * 
 * Types: text (rich text), document (PDF), video (uploaded or URL)
 */
class ModuleController extends Controller
{
    use AuthorizesRequests;

    /**
     * Extract YouTube video ID from URL
     */
    public static function extractYoutubeId($url)
    {
        // Pattern 1: youtu.be/VIDEO_ID (with or without query params)
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})(?:\?|$|&)/', $url, $matches)) {
            return $matches[1];
        }
        // Pattern 2: youtube.com/watch?v=VIDEO_ID
        if (preg_match('/youtube\.com\/watch\?.*v=([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }
        // Pattern 3: youtube.com/embed/VIDEO_ID
        if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }
        // Pattern 4: Generic YouTube pattern (fallback)
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Extract Vimeo video ID from URL
     */
    public static function extractVimeoId($url)
    {
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Validate if URL is a valid YouTube or Vimeo URL
     */
    public static function validateVideoUrl($url)
    {
        if (empty($url)) {
            return false;
        }

        // Check if it's a YouTube URL
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return self::extractYoutubeId($url) !== null;
        }

        // Check if it's a Vimeo URL
        if (str_contains($url, 'vimeo.com')) {
            return self::extractVimeoId($url) !== null;
        }

        return false;
    }
    /**
     * Show form create module dengan pilihan type
     */
    public function create(Chapter $chapter)
    {
        // Pastikan chapter milik class yang dimiliki teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && $chapter->class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized. This chapter does not belong to you.');
        }

        return view('teacher.modules.create', compact('chapter'));
    }

    /**
     * Show form create module text
     */
    public function createText(Chapter $chapter)
    {
        // Pastikan chapter milik class yang dimiliki teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && $chapter->class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized. This chapter does not belong to you.');
        }

        return view('teacher.modules.create-text', compact('chapter'));
    }

    /**
     * Show form create module document
     */
    public function createDocument(Chapter $chapter)
    {
        // Pastikan chapter milik class yang dimiliki teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && $chapter->class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized. This chapter does not belong to you.');
        }

        return view('teacher.modules.create-document', compact('chapter'));
    }

    /**
     * Show form create module video
     */
    public function createVideo(Chapter $chapter)
    {
        // Pastikan chapter milik class yang dimiliki teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && $chapter->class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized. This chapter does not belong to you.');
        }

        return view('teacher.modules.create-video', compact('chapter'));
    }

    /**
     * Store text module
     */
    public function storeText(Request $request, Chapter $chapter)
    {
        // Pastikan chapter milik class yang dimiliki teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && $chapter->class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized. This chapter does not belong to you.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
            'estimated_duration' => 'nullable|integer|min:1',
        ]);

        $validated['type'] = Module::TYPE_TEXT;
        $validated['chapter_id'] = $chapter->id;
        $validated['estimated_duration'] = $validated['estimated_duration'] ?? 0;
        $validated['approval_status'] = Module::APPROVAL_PENDING;
        $validated['is_published'] = false; // Must wait for approval

        $module = Module::create($validated);
        
        // Pastikan chapter dan class duration ter-update setelah create module
        $module->refresh();
        if ($module->chapter) {
            $module->chapter->recalculateTotalDuration();
            if ($module->chapter->class) {
                $module->chapter->class->recalculateTotalDuration();
            }
        }
        
        // Send notification to all admins
        $this->notifyAdminsForModuleApproval($module, $chapter);

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Module created successfully and is pending admin approval.');
    }

    /**
     * Store document module (PDF)
     */
    public function storeDocument(Request $request, Chapter $chapter)
    {
        // Pastikan chapter milik class yang dimiliki teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && $chapter->class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized. This chapter does not belong to you.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:50000', // 50MB max
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
            'estimated_duration' => 'nullable|integer|min:1',
        ]);

        $file = $request->file('file');
        
        // Check if file was uploaded
        if (!$file) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Please select a PDF file to upload.');
        }
        
        $fileName = Str::slug($validated['title']) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('files/pdf', $fileName, 'private');

        $module = $chapter->modules()->create([
            'title' => $validated['title'],
            'type' => Module::TYPE_DOCUMENT,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'order' => $validated['order'] ?? 0,
            'is_published' => false, // Must wait for approval
            'estimated_duration' => $validated['estimated_duration'] ?? 0,
            'approval_status' => Module::APPROVAL_PENDING,
        ]);
        
        // Send notification to all admins
        $this->notifyAdminsForModuleApproval($module, $chapter);

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Module created successfully and is pending admin approval.');
    }

    /**
     * Store video module (upload atau URL)
     */
    public function storeVideo(Request $request, Chapter $chapter)
    {
        // Pastikan chapter milik class yang dimiliki teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && $chapter->class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized. This chapter does not belong to you.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_type' => 'required|in:upload,url',
            'file' => 'required_if:video_type,upload|file|mimes:mp4,avi,mov,wmv|max:500000', // 500MB max
            'video_url' => [
                'required_if:video_type,url',
                'url',
                function ($attribute, $value, $fail) {
                    if (!empty($value) && !self::validateVideoUrl($value)) {
                        $fail('The video URL must be a valid YouTube or Vimeo URL. Supported formats: youtube.com/watch?v=..., youtu.be/..., youtube.com/embed/..., vimeo.com/...');
                    }
                },
                'nullable',
            ],
            'duration' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
            'estimated_duration' => 'nullable|integer|min:1',
        ]);

        $moduleData = [
            'title' => $validated['title'],
            'type' => Module::TYPE_VIDEO,
            'order' => $validated['order'] ?? 0,
            'is_published' => false, // Must wait for approval
            'estimated_duration' => $validated['estimated_duration'] ?? 0,
            'approval_status' => Module::APPROVAL_PENDING,
        ];

        if ($validated['video_type'] === 'upload') {
            $file = $request->file('file');
            
            // Check if file was uploaded
            if (!$file) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Please select a video file to upload.');
            }
            
            $fileName = Str::slug($validated['title']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('files/videos', $fileName, 'private');

            $moduleData['file_path'] = $path;
            $moduleData['file_name'] = $file->getClientOriginalName();
            $moduleData['mime_type'] = $file->getMimeType();
            $moduleData['file_size'] = $file->getSize();
        } else {
            $moduleData['video_url'] = $validated['video_url'];
        }

        if (isset($validated['duration'])) {
            $moduleData['duration'] = $validated['duration'];
        }

        $module = $chapter->modules()->create($moduleData);
        
        // Pastikan chapter dan class duration ter-update setelah create module
        $module->refresh();
        if ($module->chapter) {
            $module->chapter->recalculateTotalDuration();
            if ($module->chapter->class) {
                $module->chapter->class->recalculateTotalDuration();
            }
        }
        
        // Send notification to all admins
        $this->notifyAdminsForModuleApproval($module, $chapter);

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Module created successfully and is pending admin approval.');
    }

    /**
     * Show form edit module
     */
    public function edit(Chapter $chapter, Module $module)
    {
        // Pastikan module milik chapter yang dimiliki teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && ($module->chapter_id !== $chapter->id || $chapter->class->teacher_id !== auth()->id())) {
            abort(403, 'Unauthorized. This module does not belong to you.');
        }

        $view = match($module->type) {
            Module::TYPE_TEXT => 'teacher.modules.edit-text',
            Module::TYPE_DOCUMENT => 'teacher.modules.edit-document',
            Module::TYPE_VIDEO => 'teacher.modules.edit-video',
        };

        return view($view, compact('chapter', 'module'));
    }

    /**
     * Update module
     */
    public function update(Request $request, Chapter $chapter, Module $module)
    {
        // Pastikan module milik chapter yang dimiliki teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && ($module->chapter_id !== $chapter->id || $chapter->class->teacher_id !== auth()->id())) {
            abort(403, 'Unauthorized. This module does not belong to you.');
        }

        if ($module->type === Module::TYPE_TEXT) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'is_published' => 'nullable|boolean',
                'order' => 'nullable|integer|min:0',
                'estimated_duration' => 'nullable|integer|min:1',
            ]);
        } elseif ($module->type === Module::TYPE_VIDEO) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'video_url' => [
                    'nullable',
                    'url',
                    function ($attribute, $value, $fail) {
                        if (!empty($value) && !self::validateVideoUrl($value)) {
                            $fail('The video URL must be a valid YouTube or Vimeo URL. Supported formats: youtube.com/watch?v=..., youtu.be/..., youtube.com/embed/..., vimeo.com/...');
                        }
                    },
                ],
                'duration' => 'nullable|integer|min:0',
                'is_published' => 'nullable|boolean',
                'order' => 'nullable|integer|min:0',
                'estimated_duration' => 'nullable|integer|min:1',
            ]);
        } else {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'is_published' => 'nullable|boolean',
                'order' => 'nullable|integer|min:0',
                'estimated_duration' => 'nullable|integer|min:1',
            ]);
        }

        $validated['estimated_duration'] = $validated['estimated_duration'] ?? 0;

        // Teacher tidak bisa langsung publish - harus melalui approval
        $wasApproved = false;
        if (!auth()->user()->isAdmin()) {
            $validated['is_published'] = false;
            
            // Jika module sudah approved/rejected dan teacher update, set kembali ke pending
            $currentStatus = $module->approval_status ?? 'pending_approval';
            $wasApproved = in_array($currentStatus, ['approved', 'rejected']);
            
            if ($wasApproved) {
                $validated['approval_status'] = Module::APPROVAL_PENDING;
                $validated['admin_feedback'] = null; // Reset feedback jika di-edit
            } elseif ($currentStatus === 'pending_approval') {
                // Tetap pending, tidak perlu reset feedback
                $validated['approval_status'] = Module::APPROVAL_PENDING;
            }
        }

        $module->update($validated);
        
        // Pastikan chapter dan class duration ter-update setelah update module
        $module->refresh();
        if ($module->chapter) {
            $module->chapter->recalculateTotalDuration();
            if ($module->chapter->class) {
                $module->chapter->class->recalculateTotalDuration();
            }
        }
        
        // If status returns to pending after edit from approved/rejected, send notification to admin
        if (!auth()->user()->isAdmin() && $wasApproved && ($module->approval_status ?? '') === Module::APPROVAL_PENDING) {
            $this->notifyAdminsForModuleApproval($module, $chapter);
        }

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', $wasApproved 
                ? 'Module updated successfully and is pending admin approval again.' 
                : 'Module updated successfully');
    }

    /**
     * Delete module
     */
    public function destroy(Chapter $chapter, Module $module)
    {
        // Pastikan module milik chapter yang dimiliki teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && ($module->chapter_id !== $chapter->id || $chapter->class->teacher_id !== auth()->id())) {
            abort(403, 'Unauthorized. This module does not belong to you.');
        }

        // Simpan chapter_id dan class_id sebelum delete
        $chapterId = $module->chapter_id;
        $classId = $module->chapter ? $module->chapter->class_id : null;

        // Delete file if exists
        if ($module->file_path && Storage::disk('public')->exists($module->file_path)) {
            Storage::disk('public')->delete($module->file_path);
        }

        $module->delete();

        // Pastikan chapter dan class duration ter-update setelah delete module
        if ($chapterId) {
            $chapter = \App\Models\Chapter::find($chapterId);
            if ($chapter) {
                $chapter->recalculateTotalDuration();
                if ($classId) {
                    $class = \App\Models\ClassModel::find($classId);
                    if ($class) {
                        $class->recalculateTotalDuration();
                    }
                }
            }
        }

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Module deleted successfully');
    }

    /**
     * Reorder modules dalam chapter
     */
    public function reorder(Request $request, Chapter $chapter)
    {
        $this->authorize('updateChapter', $chapter);

        $validated = $request->validate([
            'modules' => 'required|array',
            'modules.*.id' => 'required|exists:modules,id',
            'modules.*.order' => 'required|integer',
        ]);

        foreach ($validated['modules'] as $moduleData) {
            Module::find($moduleData['id'])->update(['order' => $moduleData['order']]);
        }

        return response()->json(['message' => 'Modules reordered successfully']);
    }

    /**
     * Send notification to all admins for module approval
     */
    private function notifyAdminsForModuleApproval(Module $module, Chapter $chapter)
    {
        $admins = User::where('role', 'admin')->get();
        $course = $chapter->class;
        $teacher = $course->teacher;

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'module_pending_approval',
                'title' => 'Module Pending Approval',
                'message' => "Teacher '{$teacher->name}' uploaded a new module '{$module->title}' in course '{$course->name}'. Please review and approve/reject.",
                'notifiable_type' => Module::class,
                'notifiable_id' => $module->id,
            ]);
        }
    }
}
