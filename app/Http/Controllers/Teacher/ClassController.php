<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;

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
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Unauthorized');
        }

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
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Unauthorized');
        }

        return view('teacher.classes.create');
    }

    /**
     * Store class baru
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $class = auth()->user()->classes()->create($validated);

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Berhasil membuat kelas');
    }

    /**
     * Show form edit class dengan chapters
     */
    public function edit(ClassModel $class)
    {
        if ($class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $chapters = $class->chapters()->with('modules')->get();

        return view('teacher.classes.edit', compact('class', 'chapters'));
    }

    /**
     * Update class
     */
    public function update(Request $request, ClassModel $class)
    {
        if ($class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ]);

        $class->update($validated);

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Berhasil memperbarui kelas');
    }

    /**
     * Delete class
     */
    public function destroy(ClassModel $class)
    {
        if ($class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $class->delete();

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Berhasil menghapus kelas');
    }

    /**
     * Show content management interface
     */
    public function manageContent()
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Unauthorized');
        }

        $classes = ClassModel::byTeacher(auth()->id())
            ->with('chapters.modules')
            ->orderBy('order')
            ->get();

        $totalClasses = $classes->count();
        $totalChapters = $classes->sum(fn($c) => $c->chapters->count());
        $totalModules = $classes->sum(fn($c) => $c->chapters->sum(fn($ch) => $ch->modules->count() ?? 0));

        return view('teacher.manage-content', compact('classes', 'totalClasses', 'totalChapters', 'totalModules'));
    }
}
