<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Display all activities grouped by user role
     */
    public function index()
    {
        // Get activities for students
        $studentActivities = ActivityLog::with('user')
            ->whereHas('user', function($query) {
                $query->where('role', 'student');
            })
            ->latest()
            ->get();

        // Get activities for teachers
        $teacherActivities = ActivityLog::with('user')
            ->whereHas('user', function($query) {
                $query->where('role', 'teacher');
            })
            ->latest()
            ->get();

        // Get activities for admin students (users with role 'admin' but are students)
        // Assuming admin students are users with role 'admin' or we can filter differently
        $adminStudentActivities = ActivityLog::with('user')
            ->whereHas('user', function($query) {
                $query->where('role', 'admin');
            })
            ->latest()
            ->get();

        return view('admin.activities.index', compact(
            'studentActivities',
            'teacherActivities',
            'adminStudentActivities'
        ));
    }
}
