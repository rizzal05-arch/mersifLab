<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Course;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Http\Request;

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

        $data = [
            'user' => $user,
            'classes' => $classes,  // Pass with full permissions
            'totalKursus' => $totalCourses,
            'totalChapters' => $totalChapters,
            'totalModules' => $totalModules,
            'totalStudents' => $totalStudents,
            'role' => 'teacher',
            'canCreate' => true,  // Explicit permission flag for template
        ];

        return view('dashboard.teacher-content', $data);
    }

    /**
     * Get course detail untuk teacher (untuk manage/edit)
     */
    public function courseDetail($id)
    {
        $course = Course::findOrFail($id);
        
        return view('teacher.course-detail', [
            'course' => $course,
            'role' => 'teacher',
        ]);
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
