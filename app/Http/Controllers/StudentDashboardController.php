<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:student');
    }

    public function index()
    {
        $student = auth()->user();
        
        // Get published classes only (read-only data)
        $classes = ClassModel::where('is_published', true)
            ->withCount(['chapters', 'modules'])
            ->with('teacher')
            ->get();
        
        $recentModules = Module::where('is_published', true)
            ->latest()
            ->limit(6)
            ->get();
        
        return view('dashboard', [
            'role' => 'student',
            'classes' => $classes,
            'recentModules' => $recentModules,
        ]);
    }

    public function courseDetail($id)
    {
        $user = auth()->user();
        
        // Check if student is enrolled
        $isEnrolled = false;
        if ($user && $user->isStudent()) {
            $isEnrolled = DB::table('class_student')
                ->where('class_id', $id)
                ->where('user_id', $user->id)
                ->exists();
        }
        
        // Build query - only published courses, unless enrolled
        $courseQuery = ClassModel::where('id', $id);
        if (!$isEnrolled) {
            $courseQuery->where('is_published', true);
        }
        
        $course = $courseQuery->with(['teacher', 'chapters' => function($query) use ($isEnrolled) {
                if (!$isEnrolled) {
                    $query->where('is_published', true);
                }
                $query->with(['modules' => function($q) use ($isEnrolled) {
                    if (!$isEnrolled) {
                        $q->where('is_published', true);
                    }
                    $q->orderBy('order');
                }])->orderBy('order');
            }])
            ->withCount(['chapters', 'modules'])
            ->firstOrFail();
        
        // Check if course is suspended and student is not enrolled
        if (!$course->is_published && !$isEnrolled) {
            abort(403, 'This course has been suspended and is not available.');
        }

        // Hitung students count secara manual
        $course->students_count = DB::table('class_student')
            ->join('users', 'class_student.user_id', '=', 'users.id')
            ->where('class_student.class_id', $course->id)
            ->where('users.role', 'student')
            ->count();

        // Check if user is enrolled
        $isEnrolled = false;
        $progress = 0;
        $user = auth()->user();
        if ($user && $user->isStudent()) {
            $isEnrolled = $course->isEnrolledBy($user);
            if ($isEnrolled) {
                $enrollment = DB::table('class_student')
                    ->where('class_id', $course->id)
                    ->where('user_id', $user->id)
                    ->first();
                $progress = $enrollment->progress ?? 0;
            }
        }

        return view('course-detail', compact('course', 'isEnrolled', 'progress'));
    }

    public function progress()
    {
        $student = auth()->user();
        
        // Get progress dari modules yang sudah dilihat
        $viewedModules = Module::where('is_published', true)
            ->where('view_count', '>', 0)
            ->get();
        
        return view('dashboard.progress', compact('viewedModules'));
    }
}
