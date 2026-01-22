<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Notification;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all classes (courses) created by teachers
        $courses = ClassModel::with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Implementation for storing course
        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('admin.courses.show', compact('id'));
    }

    /**
     * Display course moderation page with full course hierarchy
     */
    public function moderation(string $id)
    {
        $course = ClassModel::with(['teacher', 'chapters' => function($query) {
                $query->orderBy('order');
            }, 'chapters.modules' => function($query) {
                $query->orderBy('order');
            }])
            ->withCount(['chapters', 'modules'])
            ->findOrFail($id);

        return view('admin.courses.moderation', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('admin.courses.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Implementation for updating course
        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = ClassModel::findOrFail($id);
        $courseName = $course->name;
        $teacherId = $course->teacher_id;
        
        // Send notification to teacher before deleting
        if ($teacherId) {
            Notification::create([
                'user_id' => $teacherId,
                'type' => 'course_deleted',
                'title' => 'Course Dihapus',
                'message' => "Course Anda '{$courseName}' telah dihapus oleh admin. Course ini tidak akan muncul lagi di sistem.",
                'notifiable_type' => ClassModel::class,
                'notifiable_id' => $course->id,
            ]);
        }
        
        // Delete course (cascade will handle chapters and modules)
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', "Course '{$courseName}' has been deleted successfully.");
    }
}
