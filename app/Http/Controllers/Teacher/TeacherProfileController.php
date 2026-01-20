<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $courses = $user->classes ?? collect();
        
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
        // Use empty collection for now - notifications table may not exist
        $notifications = collect();
        
        return view('teacher.notifications', compact('notifications', 'user'));
    }
}
