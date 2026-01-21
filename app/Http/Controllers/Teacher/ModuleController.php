<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Module;
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
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?))\/|youtu\.be\/)([^"&?\/\s]{11})/';
        preg_match($pattern, $url, $matches);
        return $matches[1] ?? null;
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
        ]);

        $validated['type'] = Module::TYPE_TEXT;
        $validated['chapter_id'] = $chapter->id;

        $module = Module::create($validated);

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Berhasil menambahkan module');
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
            'is_published' => $validated['is_published'] ?? false,
        ]);

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Berhasil menambahkan module');
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
            'video_url' => 'required_if:video_type,url|url|nullable',
            'duration' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ]);

        $moduleData = [
            'title' => $validated['title'],
            'type' => Module::TYPE_VIDEO,
            'order' => $validated['order'] ?? 0,
            'is_published' => $validated['is_published'] ?? false,
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

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Berhasil menambahkan module');
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
            ]);
        } elseif ($module->type === Module::TYPE_VIDEO) {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'video_url' => 'nullable|url',
                'duration' => 'nullable|integer|min:0',
                'is_published' => 'nullable|boolean',
                'order' => 'nullable|integer|min:0',
            ]);
        } else {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'is_published' => 'nullable|boolean',
                'order' => 'nullable|integer|min:0',
            ]);
        }

        $module->update($validated);

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Berhasil memperbarui module');
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

        // Delete file if exists
        if ($module->file_path && Storage::disk('public')->exists($module->file_path)) {
            Storage::disk('public')->delete($module->file_path);
        }

        $module->delete();

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
