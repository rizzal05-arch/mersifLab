<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\ClassReview;
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

        $featuredCourses = \App\Models\ClassModel::where('is_published', true)
            ->withCount(['chapters', 'modules'])
            ->with('teacher')
            ->leftJoin('class_student', 'classes.id', '=', 'class_student.class_id')
            ->select('classes.*', DB::raw('COUNT(DISTINCT class_student.user_id) as student_count'))
            ->groupBy('classes.id')
            ->orderByDesc(DB::raw('COUNT(DISTINCT class_student.user_id)'))
            ->limit(6)
            ->get();

        $data = [
            'user' => $user,
            'classes' => $classes,  // Pass with full permissions
            'featuredCourses' => $featuredCourses,
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

        // Get reviews and rating stats so the course-detail view has expected data
        $reviews = ClassReview::where('class_id', $course->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $ratingStats = [
            'total' => ClassReview::where('class_id', $course->id)->count(),
            'average' => ClassReview::where('class_id', $course->id)->avg('rating') ?? 0,
            'distribution' => []
        ];

        for ($i = 5; $i >= 1; $i--) {
            $count = ClassReview::where('class_id', $course->id)
                ->where('rating', $i)
                ->count();
            $ratingStats['distribution'][$i] = [
                'count' => $count,
                'percentage' => $ratingStats['total'] > 0 ? round(($count / $ratingStats['total']) * 100, 1) : 0
            ];
        }

        // Teacher is not a student; they won't have a user review entry
        $userReview = null;

        return view('course-detail', compact('course', 'isEnrolled', 'progress', 'userReview', 'reviews', 'ratingStats'));
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
