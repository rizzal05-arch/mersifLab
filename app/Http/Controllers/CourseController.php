<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Show all published courses (classes) in a grid
     */
    public function index()
    {
        // Get popular courses (latest published with most modules)
        $popularCourses = ClassModel::published()
            ->with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Get all courses with pagination
        $courses = ClassModel::published()
            ->with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('courses', compact('courses', 'popularCourses'));
    }

    /**
     * Show course detail page
     */
    public function detail($id)
    {
        $course = ClassModel::where('id', $id)
            ->where('is_published', true)
            ->with(['teacher', 'chapters' => function($query) {
                $query->where('is_published', true)
                    ->with(['modules' => function($q) {
                        $q->where('is_published', true)->orderBy('order');
                    }])
                    ->orderBy('order');
            }])
            ->withCount(['chapters', 'modules'])
            ->firstOrFail();

        // Hitung students count secara manual untuk menghindari masalah dengan where clause
        $course->students_count = DB::table('class_student')
            ->join('users', 'class_student.user_id', '=', 'users.id')
            ->where('class_student.class_id', $course->id)
            ->where('users.role', 'student')
            ->count();

        // Check if user is enrolled (for authenticated students)
        $isEnrolled = false;
        $progress = 0;
        if (auth()->check() && auth()->user()->isStudent()) {
            $isEnrolled = $course->isEnrolledBy(auth()->user());
            if ($isEnrolled) {
                $enrollment = DB::table('class_student')
                    ->where('class_id', $course->id)
                    ->where('user_id', auth()->id())
                    ->first();
                $progress = $enrollment->progress ?? 0;
            }
        }

        return view('course-detail', compact('course', 'isEnrolled', 'progress'));
    }
}