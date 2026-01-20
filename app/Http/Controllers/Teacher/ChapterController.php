<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * ChapterController - Manage chapters (bab) dalam class
 * 
 * Teacher hanya bisa manage chapter di class miliknya sendiri
 */
class ChapterController extends Controller
{
    use AuthorizesRequests;
    /**
     * Show list of chapters untuk class tertentu
     */
    public function index(ClassModel $class)
    {
        $this->authorize('viewClass', $class);

        $chapters = $class->chapters()->get();

        return view('teacher.chapters.index', compact('class', 'chapters'));
    }

    /**
     * Show form create chapter
     */
    public function create(ClassModel $class)
    {
        $this->authorize('createChapter', $class);

        return view('teacher.chapters.create', compact('class'));
    }

    /**
     * Store chapter baru
     */
    public function store(Request $request, ClassModel $class)
    {
        $this->authorize('createChapter', $class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $chapter = $class->chapters()->create($validated);

        return redirect()
            ->route('teacher.chapters.edit', [$class, $chapter])
            ->with('success', 'Chapter created successfully');
    }

    /**
     * Show form edit chapter
     */
    public function edit(ClassModel $class, Chapter $chapter)
    {
        $this->authorize('updateChapter', $chapter);

        return view('teacher.chapters.edit', compact('class', 'chapter'));
    }

    /**
     * Update chapter
     */
    public function update(Request $request, ClassModel $class, Chapter $chapter)
    {
        $this->authorize('updateChapter', $chapter);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ]);

        $chapter->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Chapter updated successfully');
    }

    /**
     * Delete chapter
     */
    public function destroy(ClassModel $class, Chapter $chapter)
    {
        $this->authorize('deleteChapter', $chapter);

        $chapter->delete();

        return redirect()
            ->route('teacher.chapters.index', $class)
            ->with('success', 'Chapter deleted successfully');
    }

    /**
     * Reorder chapters
     */
    public function reorder(Request $request, ClassModel $class)
    {
        $this->authorize('updateClass', $class);

        $validated = $request->validate([
            'chapters' => 'required|array',
            'chapters.*.id' => 'required|exists:chapters,id',
            'chapters.*.order' => 'required|integer',
        ]);

        foreach ($validated['chapters'] as $chapter) {
            Chapter::find($chapter['id'])->update(['order' => $chapter['order']]);
        }

        return response()->json(['message' => 'Chapters reordered successfully']);
    }
}
