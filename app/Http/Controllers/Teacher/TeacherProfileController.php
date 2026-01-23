<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Module;
use App\Models\Chapter;
use App\Models\Notification;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherProfileController extends Controller
{
    /**
     * Display teacher's profile page
     */
    public function profile()
    {
        $user = auth()->user();
        return view('teacher.profile', compact('user'));
    }

    /**
     * Update teacher's profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);
        
        $user->update($validated);
        
        return redirect()->route('teacher.profile')->with('success', 'Profile updated successfully');
    }

    /**
     * Display teacher's courses (classes)
     */
    public function myCourses()
    {
        $user = auth()->user();
        $courses = \App\Models\ClassModel::where('teacher_id', $user->id)
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('teacher.my-courses', compact('courses', 'user'));
    }

    /**
     * Display teacher's purchase history
     */
    public function purchaseHistory()
    {
        $user = auth()->user();
        $purchases = collect(); // Empty for now - teachers don't have purchase history
        
        return view('teacher.purchase-history', compact('purchases', 'user'));
    }

    /**
     * Display teacher's notifications
     */
    public function notifications()
    {
        $user = auth()->user();
        // Load notifications from database
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('teacher.notifications', compact('notifications', 'user'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        $user = auth()->user();
        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Display teacher's statistics
     */
    public function statistics()
    {
        $user = auth()->user();
        
        // Get teacher's classes
        $classes = ClassModel::where('teacher_id', $user->id)
            ->withCount(['chapters', 'modules'])
            ->get();
        
        // Calculate statistics
        $totalCourses = $classes->count();
        $totalChapters = $classes->sum('chapters_count');
        $totalModules = $classes->sum('modules_count');
        
        // Get total students enrolled in teacher's courses
        $totalStudents = DB::table('class_student')
            ->join('classes', 'class_student.class_id', '=', 'classes.id')
            ->join('users', 'class_student.user_id', '=', 'users.id')
            ->where('classes.teacher_id', $user->id)
            ->where('users.role', 'student')
            ->distinct('class_student.user_id')
            ->count('class_student.user_id');
        
        // Get total enrollments (can be more than total students if one student enrolls in multiple courses)
        $totalEnrollments = DB::table('class_student')
            ->join('classes', 'class_student.class_id', '=', 'classes.id')
            ->join('users', 'class_student.user_id', '=', 'users.id')
            ->where('classes.teacher_id', $user->id)
            ->where('users.role', 'student')
            ->count();
        
        // Calculate average completion rate
        $enrollmentsWithProgress = DB::table('class_student')
            ->join('classes', 'class_student.class_id', '=', 'classes.id')
            ->where('classes.teacher_id', $user->id)
            ->select('class_student.progress')
            ->get();
        
        $avgCompletionRate = $enrollmentsWithProgress->count() > 0 
            ? round($enrollmentsWithProgress->avg('progress'), 1) 
            : 0;
        
        // Get enrollment trend (last 6 months)
        $enrollmentTrend = DB::table('class_student')
            ->join('classes', 'class_student.class_id', '=', 'classes.id')
            ->where('classes.teacher_id', $user->id)
            ->select(
                DB::raw('DATE_FORMAT(class_student.enrolled_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('class_student.enrolled_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Get top courses by enrollment
        $topCourses = DB::table('class_student')
            ->join('classes', 'class_student.class_id', '=', 'classes.id')
            ->where('classes.teacher_id', $user->id)
            ->select(
                'classes.id',
                'classes.name',
                'classes.is_published',
                DB::raw('COUNT(class_student.id) as enrollments')
            )
            ->groupBy('classes.id', 'classes.name', 'classes.is_published')
            ->orderBy('enrollments', 'desc')
            ->limit(5)
            ->get();
        
        // Get student performance data
        $studentPerformance = DB::table('class_student')
            ->join('classes', 'class_student.class_id', '=', 'classes.id')
            ->join('users', 'class_student.user_id', '=', 'users.id')
            ->where('classes.teacher_id', $user->id)
            ->where('users.role', 'student')
            ->select(
                'users.name as student_name',
                'classes.name as course_name',
                'class_student.progress',
                'class_student.completed_at'
            )
            ->orderBy('class_student.progress', 'desc')
            ->limit(10)
            ->get();
        
        return view('teacher.statistics', compact(
            'user',
            'totalCourses',
            'totalChapters',
            'totalModules',
            'totalStudents',
            'totalEnrollments',
            'avgCompletionRate',
            'enrollmentTrend',
            'topCourses',
            'studentPerformance'
        ));
    }

    /**
     * Display notification preferences for teacher
     */
    public function notificationPreferences()
    {
        $user = auth()->user();
        $preferences = $user->getNotificationPreference();
        
        return view('teacher.notification-preferences', compact('preferences'));
    }

    /**
     * Update notification preferences for teacher
     */
    public function updateNotificationPreferences(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'new_course' => 'nullable|boolean',
            'new_chapter' => 'nullable|boolean',
            'new_module' => 'nullable|boolean',
            'module_approved' => 'nullable|boolean',
            'student_enrolled' => 'nullable|boolean',
            'course_rated' => 'nullable|boolean',
            'course_completed' => 'nullable|boolean',
            'announcements' => 'nullable|boolean',
            'promotions' => 'nullable|boolean',
            'course_recommendations' => 'nullable|boolean',
            'learning_stats' => 'nullable|boolean',
        ]);

        // Convert checkbox values to boolean
        $preferencesData = [];
        foreach ($validated as $key => $value) {
            $preferencesData[$key] = $request->has($key) ? true : false;
        }

        $preference = $user->getNotificationPreference();
        $preference->update($preferencesData);

        return redirect()->route('teacher.notification-preferences')->with('success', 'Notification preferences updated successfully');
    }
}
