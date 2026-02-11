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

        // Prevent creating module in course that is pending approval
        if ($chapter->class->isPendingApproval()) {
            return redirect()->route('teacher.manage.content')
                ->with('error', 'Course ini sedang dalam proses persetujuan admin dan tidak dapat diubah. Silakan tunggu hingga persetujuan selesai.');
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

        // Prevent creating module in course that is pending approval
        if ($chapter->class->isPendingApproval()) {
            return redirect()->route('teacher.manage.content')
                ->with('error', 'Course ini sedang dalam proses persetujuan admin dan tidak dapat diubah. Silakan tunggu hingga persetujuan selesai.');
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

        // Prevent creating module in course that is pending approval
        if ($chapter->class->isPendingApproval()) {
            return redirect()->route('teacher.manage.content')
                ->with('error', 'Course ini sedang dalam proses persetujuan admin dan tidak dapat diubah. Silakan tunggu hingga persetujuan selesai.');
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

        // Prevent creating module in course that is pending approval
        if ($chapter->class->isPendingApproval()) {
            return redirect()->route('teacher.manage.content')
                ->with('error', 'Course ini sedang dalam proses persetujuan admin dan tidak dapat diubah. Silakan tunggu hingga persetujuan selesai.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
            'estimated_duration' => 'required|integer|min:1',
        ]);

        $validated['type'] = Module::TYPE_TEXT;
        $validated['chapter_id'] = $chapter->id;
        $validated['estimated_duration'] = $validated['estimated_duration'] ?? 0;
        // Module follows course approval workflow - no individual module approval needed
        $validated['approval_status'] = Module::APPROVAL_APPROVED; // Auto-approved since course controls visibility
        $validated['is_published'] = false; // Published status follows course status

        $module = Module::create($validated);
        
        // Pastikan chapter dan class duration ter-update setelah create module
        $module->refresh();
        if ($module->chapter) {
            $module->chapter->recalculateTotalDuration();
            if ($module->chapter->class) {
                $module->chapter->class->recalculateTotalDuration();
            }
        }
        
        auth()->user()->logActivity('module_created', "Menambahkan modul: {$module->title} di kelas {$chapter->class->name}");
        
        // Tidak perlu notifikasi ke admin - approval dilakukan per course level

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Module berhasil dibuat!');
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

        // Prevent creating module in course that is pending approval
        if ($chapter->class->isPendingApproval()) {
            return redirect()->route('teacher.manage.content')
                ->with('error', 'Course ini sedang dalam proses persetujuan admin dan tidak dapat diubah. Silakan tunggu hingga persetujuan selesai.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:50000', // 50MB max
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
            'estimated_duration' => 'required|integer|min:1',
        ], [
            'title.required' => 'Judul module tidak boleh kosong',
            'title.max' => 'Judul module tidak boleh lebih dari 255 karakter',
            'file.required' => 'File PDF tidak boleh kosong',
            'file.file' => 'File harus berupa file yang valid',
            'file.mimes' => 'Format file yang diperbolehkan hanya PDF',
            'file.max' => 'Ukuran file tidak boleh lebih dari 50MB',
            'estimated_duration.required' => 'Estimasi durasi tidak boleh kosong',
            'estimated_duration.min' => 'Estimasi durasi minimal 1 menit',
        ]);

        $file = $request->file('file');
        
        // Additional file size check (in bytes: 50MB = 50 * 1024 * 1024)
        if ($file && $file->getSize() > 50 * 1024 * 1024) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['file' => 'Ukuran file tidak boleh lebih dari 50MB']);
        }
        
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
            'is_published' => false, // Published status follows course status
            'estimated_duration' => $validated['estimated_duration'] ?? 0,
            'approval_status' => Module::APPROVAL_APPROVED, // Auto-approved since course controls visibility
        ]);
        
        auth()->user()->logActivity('module_created', "Menambahkan modul: {$module->title} di kelas {$chapter->class->name}");
        
        // Tidak perlu notifikasi ke admin - approval dilakukan per course level

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Module berhasil dibuat!');
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

        // Prevent creating module in course that is pending approval
        if ($chapter->class->isPendingApproval()) {
            return redirect()->route('teacher.manage.content')
                ->with('error', 'Course ini sedang dalam proses persetujuan admin dan tidak dapat diubah. Silakan tunggu hingga persetujuan selesai.');
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
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
            'estimated_duration' => 'required|integer|min:1',
        ], [
            'title.required' => 'Judul module tidak boleh kosong',
            'title.max' => 'Judul module tidak boleh lebih dari 255 karakter',
            'video_type.required' => 'Tipe video harus dipilih',
            'video_type.in' => 'Tipe video tidak valid',
            'file.required_if' => 'File video harus diupload jika tipe video adalah upload',
            'file.file' => 'File harus berupa file yang valid',
            'file.mimes' => 'Format video yang diperbolehkan: mp4, avi, mov, wmv',
            'file.max' => 'Ukuran file video tidak boleh lebih dari 500MB',
            'video_url.required_if' => 'URL video harus diisi jika tipe video adalah URL',
            'video_url.url' => 'Format URL tidak valid',
            'estimated_duration.required' => 'Estimasi durasi tidak boleh kosong',
            'estimated_duration.min' => 'Estimasi durasi minimal 1 menit',
        ]);

        $moduleData = [
            'title' => $validated['title'],
            'type' => Module::TYPE_VIDEO,
            'order' => $validated['order'] ?? 0,
            'is_published' => false, // Published status follows course status
            'estimated_duration' => $validated['estimated_duration'] ?? 0,
            'approval_status' => Module::APPROVAL_APPROVED, // Auto-approved since course controls visibility
        ];

        if ($validated['video_type'] === 'upload') {
            $file = $request->file('file');
            
            // Additional file size check (in bytes: 500MB = 500 * 1024 * 1024)
            if ($file && $file->getSize() > 500 * 1024 * 1024) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['file' => 'Ukuran file video tidak boleh lebih dari 500MB']);
            }
            
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

        $module = $chapter->modules()->create($moduleData);
        
        // Pastikan chapter dan class duration ter-update setelah create module
        $module->refresh();
        if ($module->chapter) {
            $module->chapter->recalculateTotalDuration();
            if ($module->chapter->class) {
                $module->chapter->class->recalculateTotalDuration();
            }
        }
        
        auth()->user()->logActivity('module_created', "Menambahkan modul: {$module->title} di kelas {$chapter->class->name}");
        
        // Tidak perlu notifikasi ke admin - approval dilakukan per course level

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Module berhasil dibuat!');
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

        // Prevent editing module in course that is pending approval
        if ($chapter->class->isPendingApproval()) {
            return redirect()->route('teacher.manage.content')
                ->with('error', 'Course ini sedang dalam proses persetujuan admin dan tidak dapat diubah. Silakan tunggu hingga persetujuan selesai.');
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

        // Prevent updating module in course that is pending approval
        if ($chapter->class->isPendingApproval()) {
            return redirect()->route('teacher.manage.content')
                ->with('error', 'Course ini sedang dalam proses persetujuan admin dan tidak dapat diubah. Silakan tunggu hingga persetujuan selesai.');
        }

        if ($module->type === Module::TYPE_TEXT) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'is_published' => 'nullable|boolean',
                'order' => 'nullable|integer|min:0',
                'estimated_duration' => 'nullable|integer|min:1',
            ], [
                'title.required' => 'Judul module tidak boleh kosong',
                'title.max' => 'Judul module tidak boleh lebih dari 255 karakter',
                'content.required' => 'Konten module tidak boleh kosong',
                'estimated_duration.min' => 'Estimasi durasi minimal 1 menit',
            ]);
        } elseif ($module->type === Module::TYPE_VIDEO) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'video_url' => [
                    'url',
                    function ($attribute, $value, $fail) {
                        if (!empty($value) && !self::validateVideoUrl($value)) {
                            $fail('The video URL must be a valid YouTube or Vimeo URL. Supported formats: youtube.com/watch?v=..., youtu.be/..., youtube.com/embed/..., vimeo.com/...');
                        }
                    },
                    'nullable',
                ],
                'is_published' => 'nullable|boolean',
                'order' => 'nullable|integer|min:0',
                'estimated_duration' => 'required|integer|min:1',
                'replace_video' => 'nullable|boolean',
                'new_video_type' => 'required_if:replace_video,1|in:upload,url',
                'new_file' => 'required_if:new_video_type,upload|nullable|file|mimes:mp4,avi,mov,wmv|max:500000',
                'new_video_url' => [
                    'required_if:new_video_type,url',
                    'url',
                    function ($attribute, $value, $fail) {
                        if (!empty($value) && !self::validateVideoUrl($value)) {
                            $fail('The video URL must be a valid YouTube or Vimeo URL. Supported formats: youtube.com/watch?v=..., youtu.be/..., youtube.com/embed/..., vimeo.com/...');
                        }
                    },
                    'nullable',
                ],
            ], [
                'title.required' => 'Judul module tidak boleh kosong',
                'title.max' => 'Judul module tidak boleh lebih dari 255 karakter',
                'video_url.url' => 'Format URL tidak valid',
                'estimated_duration.required' => 'Estimasi durasi tidak boleh kosong',
                'estimated_duration.min' => 'Estimasi durasi minimal 1 menit',
                'new_video_type.required_if' => 'Tipe video baru harus dipilih jika mengganti video',
                'new_video_type.in' => 'Tipe video tidak valid',
                'new_file.required_if' => 'File video harus diupload jika tipe video adalah upload',
                'new_file.file' => 'File harus berupa file yang valid',
                'new_file.mimes' => 'Format video yang diperbolehkan: mp4, avi, mov, wmv',
                'new_file.max' => 'Ukuran file video tidak boleh lebih dari 500MB',
                'new_video_url.required_if' => 'URL video harus diisi jika tipe video adalah URL',
                'new_video_url.url' => 'Format URL tidak valid',
            ]);
        } elseif ($module->type === Module::TYPE_DOCUMENT) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'file' => 'nullable|file|mimes:pdf|max:50000', // 50MB max for replacement
                'is_published' => 'nullable|boolean',
                'order' => 'nullable|integer|min:0',
                'estimated_duration' => 'nullable|integer|min:1',
            ], [
                'title.required' => 'Judul module tidak boleh kosong',
                'title.max' => 'Judul module tidak boleh lebih dari 255 karakter',
                'file.file' => 'File harus berupa file yang valid',
                'file.mimes' => 'Format file yang diperbolehkan hanya PDF',
                'file.max' => 'Ukuran file tidak boleh lebih dari 50MB',
                'estimated_duration.min' => 'Estimasi durasi minimal 1 menit',
            ]);
        } else {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'is_published' => 'nullable|boolean',
                'order' => 'nullable|integer|min:0',
                'estimated_duration' => 'nullable|integer|min:1',
                'replace_file' => 'nullable|boolean',
                'replace_video' => 'nullable|boolean',
                'new_video_type' => 'required_if:replace_video,1|in:upload,url',
                'new_file' => 'required_if:new_video_type,upload|file|mimes:mp4,avi,mov,wmv|max:500000',
                'new_video_url' => [
                    'required_if:new_video_type,url',
                    'url',
                    function ($attribute, $value, $fail) {
                        if (!empty($value) && !self::validateVideoUrl($value)) {
                            $fail('The video URL must be a valid YouTube or Vimeo URL. Supported formats: youtube.com/watch?v=..., youtu.be/..., youtube.com/embed/..., vimeo.com/...');
                        }
                    },
                    'nullable',
                ],
            ], [
                'title.required' => 'Judul module tidak boleh kosong',
                'title.max' => 'Judul module tidak boleh lebih dari 255 karakter',
                'estimated_duration.min' => 'Estimasi durasi minimal 1 menit',
                'new_video_type.required_if' => 'Tipe video baru harus dipilih jika mengganti video',
                'new_video_type.in' => 'Tipe video tidak valid',
                'new_file.required_if' => 'File video harus diupload jika tipe video adalah upload',
                'new_file.file' => 'File harus berupa file yang valid',
                'new_file.mimes' => 'Format video yang diperbolehkan: mp4, avi, mov, wmv',
                'new_file.max' => 'Ukuran file video tidak boleh lebih dari 500MB',
                'new_video_url.required_if' => 'URL video harus diisi jika tipe video adalah URL',
                'new_video_url.url' => 'Format URL tidak valid',
            ]);
        }

        $validated['estimated_duration'] = $validated['estimated_duration'] ?? 0;

        // Handle file replacement for document and video modules
        $replaceFile = $request->boolean('replace_file');
        $replaceVideo = $request->boolean('replace_video');
        $videoReplaced = false;
        
        if ($replaceFile && $request->hasFile('file')) {
            $file = $request->file('file');
            
            // Additional file size check based on module type
            if ($module->type === Module::TYPE_DOCUMENT) {
                if ($file->getSize() > 50 * 1024 * 1024) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['file' => 'Ukuran file tidak boleh lebih dari 50MB']);
                }
            } elseif ($module->type === Module::TYPE_VIDEO) {
                if ($file->getSize() > 500 * 1024 * 1024) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['file' => 'Ukuran file video tidak boleh lebih dari 500MB']);
                }
            }
            
            // Delete old file if exists
            if ($module->file_path && Storage::disk('private')->exists($module->file_path)) {
                Storage::disk('private')->delete($module->file_path);
            }
            
            // Store new file
            $folder = $module->type === Module::TYPE_DOCUMENT ? 'files/pdf' : 'files/videos';
            $fileName = Str::slug($validated['title']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($folder, $fileName, 'private');
            
            // Update file data
            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['mime_type'] = $file->getMimeType();
            $validated['file_size'] = $file->getSize();
            // Module follows course approval workflow - no individual module approval needed
        } elseif (!$replaceFile) {
            // Jika tidak mengganti file, hapus file dari validated untuk tidak di-update
            unset($validated['file_path'], $validated['file_name'], $validated['mime_type'], $validated['file_size']);
        }

        // Handle video replacement
        if ($replaceVideo && $module->type === Module::TYPE_VIDEO) {
            $newVideoType = $validated['new_video_type'] ?? null;
            
            if ($newVideoType === 'upload' && $request->hasFile('new_file')) {
                $file = $request->file('new_file');
                
                // File size check
                if ($file->getSize() > 500 * 1024 * 1024) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->withErrors(['new_file' => 'Ukuran file video tidak boleh lebih dari 500MB']);
                }
                
                // Delete old file if exists
                if ($module->file_path && Storage::disk('private')->exists($module->file_path)) {
                    Storage::disk('private')->delete($module->file_path);
                }
                
                // Store new video file
                $fileName = Str::slug($validated['title']) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('files/videos', $fileName, 'private');
                
                // Update video file data
                $validated['file_path'] = $path;
                $validated['file_name'] = $file->getClientOriginalName();
                $validated['mime_type'] = $file->getMimeType();
                $validated['file_size'] = $file->getSize();
                $validated['video_url'] = null; // Clear URL if switching to file
                
                $videoReplaced = true;
            } elseif ($newVideoType === 'url' && !empty($validated['new_video_url'])) {
                // Delete old file if switching to URL
                if ($module->file_path && Storage::disk('private')->exists($module->file_path)) {
                    Storage::disk('private')->delete($module->file_path);
                }
                
                // Update video URL
                $validated['video_url'] = $validated['new_video_url'];
                $validated['file_path'] = null;
                $validated['file_name'] = null;
                $validated['mime_type'] = null;
                $validated['file_size'] = null;
                
                $videoReplaced = true;
            }
            
            // Module follows course approval workflow - no individual module approval needed
        }
        
        // Clean up temporary fields
        unset($validated['replace_file'], $validated['replace_video'], $validated['new_video_type'], $validated['new_file'], $validated['new_video_url']);

        // Module follows course approval workflow
        if (!auth()->user()->isAdmin()) {
            $validated['is_published'] = false; // Published status follows course status
            $validated['approval_status'] = Module::APPROVAL_APPROVED; // Auto-approved since course controls visibility
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
        
        auth()->user()->logActivity('module_updated', "Mengubah modul: {$module->title} di kelas " . ($module->chapter->class->name ?? ''));

        // Tidak perlu notifikasi ke admin - approval dilakukan per course level

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Module berhasil diperbarui!');
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

        // Prevent deleting module in course that is pending approval
        if ($chapter->class->isPendingApproval()) {
            return redirect()->route('teacher.manage.content')
                ->with('error', 'Course ini sedang dalam proses persetujuan admin dan tidak dapat diubah. Silakan tunggu hingga persetujuan selesai.');
        }

        // Simpan chapter_id dan class_id sebelum delete
        $chapterId = $module->chapter_id;
        $classId = $module->chapter ? $module->chapter->class_id : null;
        $moduleTitle = $module->title;
        $className = $module->chapter && $module->chapter->class ? $module->chapter->class->name : '';

        // Delete file if exists
        if ($module->file_path && Storage::disk('private')->exists($module->file_path)) {
            Storage::disk('private')->delete($module->file_path);
        }

        $module->delete();

        if (auth()->user()->isTeacher()) {
            auth()->user()->logActivity('module_deleted', "Menghapus modul: {$moduleTitle}" . ($className ? " di kelas {$className}" : ''));
        }

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
            ->with('success', 'Berhasil menghapus module');
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

    }
