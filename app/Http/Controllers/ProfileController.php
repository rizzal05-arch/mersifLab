<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        // Update profile logic
        return redirect()->route('profile')->with('success', 'Profile updated successfully');
    }

    public function myCourses()
    {
        $user = auth()->user();
        
        // If user is a teacher, redirect to teacher courses
        if ($user->isTeacher()) {
            return redirect()->route('teacher.courses');
        }
        
        // For students, show published courses they can enroll in
        // Note: In the future, this should show enrolled courses when enrollment system is implemented
        $courses = \App\Models\ClassModel::published()
            ->with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        return view('profile.my-courses', compact('courses'));
    }

    public function purchaseHistory()
    {
        return view('profile.purchase-history');
    }

    public function invoice($id)
    {
        return view('profile.invoice', compact('id'));
    }

    public function notificationPreferences()
    {
        return view('profile.notification-preferences');
    }

    public function updateNotificationPreferences(Request $request)
    {
        // Update notification preferences logic
        return redirect()->route('notification-preferences')->with('success', 'Preferences updated successfully');
    }
}
