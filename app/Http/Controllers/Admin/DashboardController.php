<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        // Calculate stats
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalCourses = ClassModel::count();

        // Fetch latest 5 activity logs with user relation
        $activities = ActivityLog::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Fetch top 5 courses (classes) ordered by total_sales (desc)
        // Eager load: teacher (User), and count sections/modules
        $topCoursesQuery = ClassModel::with(['teacher'])
            ->withCount(['chapters as sections_count', 'modules as total_materi']);
        
        // Filter by active status if status column exists
        if (Schema::hasColumn('classes', 'status')) {
            $topCoursesQuery->where('status', 'active');
        }
        
        // Order by total_sales if column exists, otherwise by created_at
        if (Schema::hasColumn('classes', 'total_sales')) {
            $topCoursesQuery->orderBy('total_sales', 'desc');
        } else {
            $topCoursesQuery->orderBy('created_at', 'desc');
        }
        
        $topCourses = $topCoursesQuery->take(5)->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'totalCourses',
            'activities',
            'topCourses'
        ));
    }
}
