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
        $user = auth()->user();
        $isTeacherOrAdmin = $user && ($user->isTeacher() || $user->isAdmin());
        
        // Build query
        $courseQuery = ClassModel::where('id', $id);
        
        // If not teacher/admin, only show published courses
        // Teacher can see their own courses even if not published
        if (!$isTeacherOrAdmin) {
            $courseQuery->where('is_published', true);
        } elseif ($user->isTeacher() && !$user->isAdmin()) {
            // Teacher can only see their own courses if not published
            $courseQuery->where(function($q) use ($user) {
                $q->where('is_published', true)
                  ->orWhere('teacher_id', $user->id);
            });
        }
        
        $course = $courseQuery->with(['teacher', 'chapters' => function($query) use ($isTeacherOrAdmin, $user, $id) {
                // If not teacher/admin, only show published chapters
                if (!$isTeacherOrAdmin) {
                    $query->where('is_published', true);
                } elseif ($user && $user->isTeacher() && !$user->isAdmin()) {
                    // Teacher can see chapters of their own course even if not published
                    $query->whereHas('class', function($q) use ($user) {
                        $q->where('teacher_id', $user->id);
                    });
                }
                $query->with(['modules' => function($q) use ($isTeacherOrAdmin, $user) {
                    if (!$isTeacherOrAdmin) {
                        $q->where('is_published', true);
                    }
                    $q->orderBy('order');
                }])->orderBy('order');
            }])
            ->withCount(['chapters', 'modules'])
            ->firstOrFail();
        
        // Check if course is suspended and user is not the owner/admin
        if (!$course->is_published && $user && !$user->isAdmin() && $course->teacher_id !== $user->id) {
            abort(403, 'This course has been suspended and is not available.');
        }

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