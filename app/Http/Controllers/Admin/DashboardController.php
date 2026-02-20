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

        // Fetch top 5 courses (classes) ordered by actual purchase count
        // Eager load: teacher (User), and count chapters/modules and purchases
        $topCoursesQuery = ClassModel::with(['teacher'])
            ->withCount(['chapters as sections_count', 'chapters as chapters_count', 'modules'])
            ->withCount(['purchases' => function($query) {
                $query->where('status', 'success');
            }]);
        
        // Filter by published status if status column exists
        if (Schema::hasColumn('classes', 'status')) {
            $topCoursesQuery->where('status', 'published');
        }
        
        // Order by actual purchase count instead of total_sales column
        $topCoursesQuery->orderBy('purchases_count', 'desc');
        
        $topCourses = $topCoursesQuery->take(5)->get();

        $response = response()->view('admin.dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'totalCourses',
            'activities',
            'topCourses'
        ));
        
        // Add cache control headers to prevent back button after logout
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        
        return $response;
    }
}
