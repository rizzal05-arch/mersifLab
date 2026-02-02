<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        $studentActivities = ActivityLog::with('user')
            ->whereHas('user', function($query) {
                $query->where('role', 'student');
            })
            ->latest()
            ->get();

        $teacherActivities = ActivityLog::with('user')
            ->whereHas('user', function($query) {
                $query->where('role', 'teacher');
            })
            ->latest()
            ->get();

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

    public function userActivities(string $userId)
    {
        $user = User::findOrFail($userId);
        
        $activities = ActivityLog::where('user_id', $userId)
            ->with('subject')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.activities.user', compact('user', 'activities'));
    }
}
