<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Materi;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherDashboardController extends Controller
{
    /**
     * Display teacher dashboard with content management
     * 
     * Loads teacher's own classes with CRUD permissions
     */
    public function index()
    {
        $user = auth()->user();
        
        // Load teacher's classes with chapter and module counts
        $classes = ClassModel::where('teacher_id', $user->id)
            ->withCount(['chapters', 'modules'])
            ->with('teacher')
            ->get();
        
        $totalCourses = $classes->count();
        $totalChapters = $classes->sum('chapters_count');
        $totalModules = $classes->sum('modules_count');
        
        // Hitung total student yang enroll
        $totalStudents = User::where('role', 'student')->count();

        // Get notifications for teacher
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $unreadNotificationsCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        $data = [
            'user' => $user,
            'classes' => $classes,  // Pass with full permissions
            'totalKursus' => $totalCourses,
            'totalChapters' => $totalChapters,
            'totalModules' => $totalModules,
            'totalStudents' => $totalStudents,
            'role' => 'teacher',
            'canCreate' => true,  // Explicit permission flag for template
            'notifications' => $notifications,
            'unreadNotificationsCount' => $unreadNotificationsCount,
        ];

        return view('teacher.dashboard', $data);
    }

    /**
     * Get course detail untuk teacher (untuk manage/edit)
     */
    public function courseDetail($id)
    {
        $user = auth()->user();
        
        // Teacher can see their own courses (published or draft)
        $course = ClassModel::where('id', $id)
            ->where('teacher_id', $user->id)
            ->with(['teacher', 'chapters' => function($query) {
                $query->with(['modules' => function($q) {
                    $q->orderBy('order');
                }])->orderBy('order');
            }])
            ->withCount(['chapters', 'modules'])
            ->firstOrFail();

        // Hitung students count secara manual
        $course->students_count = DB::table('class_student')
            ->join('users', 'class_student.user_id', '=', 'users.id')
            ->where('class_student.class_id', $course->id)
            ->where('users.role', 'student')
            ->count();

        // Teacher is always "enrolled" in their own courses
        $isEnrolled = true;
        $progress = 100; // Teacher has full access

        return view('course-detail', compact('course', 'isEnrolled', 'progress'));
    }

    /**
     * Get analytics/statistics untuk teacher
     */
    public function analytics()
    {
        $user = auth()->user();
        
        return view('teacher.analytics', [
            'user' => $user,
            'role' => 'teacher',
        ]);
    }

    /**
     * Manage materi
     */
    public function materiManagement()
    {
        $materials = Materi::all();
        
        return view('teacher.materi-management', [
            'materials' => $materials,
            'role' => 'teacher',
        ]);
    }
}
