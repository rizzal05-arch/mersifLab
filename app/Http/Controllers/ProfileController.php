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
        $courses = $user->courses ?? collect();
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
