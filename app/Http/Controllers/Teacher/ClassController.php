<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * ClassController - Manage classes (kursus/pembelajaran)
 * 
 * Teacher hanya bisa manage class miliknya sendiri
 */
class ClassController extends Controller
{
    use AuthorizesRequests;
    /**
     * Show list of classes milik teacher
     */
    public function index()
    {
        $this->authorize('manageContent');

        $classes = ClassModel::byTeacher(auth()->id())
            ->orderBy('order')
            ->get();

        return view('teacher.classes.index', compact('classes'));
    }

    /**
     * Show form create class
     */
    public function create()
    {
        $this->authorize('createClass');

        return view('teacher.classes.create');
    }

    /**
     * Store class baru
     */
    public function store(Request $request)
    {
        $this->authorize('createClass');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $class = auth()->user()->classes()->create($validated);

        return redirect()
            ->route('teacher.classes.edit', $class)
            ->with('success', 'Class created successfully');
    }

    /**
     * Show form edit class dengan chapters
     */
    public function edit(ClassModel $class)
    {
        $this->authorize('updateClass', $class);

        $chapters = $class->chapters()->with('modules')->get();

        return view('teacher.classes.edit', compact('class', 'chapters'));
    }

    /**
     * Update class
     */
    public function update(Request $request, ClassModel $class)
    {
        $this->authorize('updateClass', $class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ]);

        $class->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Class updated successfully');
    }

    /**
     * Delete class
     */
    public function destroy(ClassModel $class)
    {
        $this->authorize('deleteClass', $class);

        $class->delete();

        return redirect()
            ->route('teacher.classes.index')
            ->with('success', 'Class deleted successfully');
    }

    /**
     * Show content management interface
     */
    public function manageContent()
    {
        $this->authorize('manageContent');

        $classes = ClassModel::byTeacher(auth()->id())
            ->with('chapters.modules')
            ->orderBy('order')
            ->get();

        $totalClasses = $classes->count();
        $totalChapters = $classes->sum(fn($c) => $c->chapters->count());
        $totalModules = $classes->sum(fn($c) => $c->modules->count() ?? 0);

        return view('teacher.manage-content', compact('classes', 'totalClasses', 'totalChapters', 'totalModules'));
    }
}
