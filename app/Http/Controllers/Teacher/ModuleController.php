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
     * Show form create module dengan pilihan type
     */
    public function create(Chapter $chapter)
    {
        $this->authorize('createModule', $chapter);

        return view('teacher.modules.create', compact('chapter'));
    }

    /**
     * Show form create module text
     */
    public function createText(Chapter $chapter)
    {
        $this->authorize('createModule', $chapter);

        return view('teacher.modules.create-text', compact('chapter'));
    }

    /**
     * Show form create module document
     */
    public function createDocument(Chapter $chapter)
    {
        $this->authorize('createModule', $chapter);

        return view('teacher.modules.create-document', compact('chapter'));
    }

    /**
     * Show form create module video
     */
    public function createVideo(Chapter $chapter)
    {
        $this->authorize('createModule', $chapter);

        return view('teacher.modules.create-video', compact('chapter'));
    }

    /**
     * Store text module
     */
    public function storeText(Request $request, Chapter $chapter)
    {
        $this->authorize('createModule', $chapter);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $validated['type'] = Module::TYPE_TEXT;
        $validated['chapter_id'] = $chapter->id;

        $module = Module::create($validated);

        return redirect()
            ->route('teacher.modules.edit', [$chapter, $module])
            ->with('success', 'Text module created successfully');
    }

    /**
     * Store document module (PDF)
     */
    public function storeDocument(Request $request, Chapter $chapter)
    {
        $this->authorize('createModule', $chapter);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:50000', // 50MB max
            'order' => 'nullable|integer|min:0',
        ]);

        $file = $request->file('file');
        $fileName = Str::slug($validated['title']) . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('modules/documents', $fileName, 'public');

        $module = $chapter->modules()->create([
            'title' => $validated['title'],
            'type' => Module::TYPE_DOCUMENT,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'order' => $validated['order'] ?? 0,
        ]);

        return redirect()
            ->route('teacher.modules.edit', [$chapter, $module])
            ->with('success', 'Document module created successfully');
    }

    /**
     * Store video module (upload atau URL)
     */
    public function storeVideo(Request $request, Chapter $chapter)
    {
        $this->authorize('createModule', $chapter);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_type' => 'required|in:upload,url',
            'file' => 'required_if:video_type,upload|file|mimes:mp4,avi,mov,wmv|max:500000', // 500MB max
            'video_url' => 'required_if:video_type,url|url|nullable',
            'duration' => 'nullable|integer|min:0',
            'order' => 'nullable|integer|min:0',
        ]);

        $moduleData = [
            'title' => $validated['title'],
            'type' => Module::TYPE_VIDEO,
            'order' => $validated['order'] ?? 0,
        ];

        if ($validated['video_type'] === 'upload') {
            $file = $request->file('file');
            $fileName = Str::slug($validated['title']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('modules/videos', $fileName, 'public');

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
            ->route('teacher.modules.edit', [$chapter, $module])
            ->with('success', 'Video module created successfully');
    }

    /**
     * Show form edit module
     */
    public function edit(Chapter $chapter, Module $module)
    {
        $this->authorize('updateModule', $module);

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
        $this->authorize('updateModule', $module);

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
            ->back()
            ->with('success', 'Module updated successfully');
    }

    /**
     * Delete module
     */
    public function destroy(Chapter $chapter, Module $module)
    {
        $this->authorize('deleteModule', $module);

        // Delete file if exists
        if ($module->file_path && Storage::disk('public')->exists($module->file_path)) {
            Storage::disk('public')->delete($module->file_path);
        }

        $module->delete();

        return redirect()
            ->route('teacher.chapters.edit', [$chapter->class_id, $chapter])
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
}
