<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NotificationPreference;
use App\Models\Purchase;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'biography' => 'nullable|string',
        ]);
        
        $user->update($validated);
        
        return redirect()->route('profile')->with('success', 'Profile updated successfully');
    }

    public function myCourses()
    {
        $user = auth()->user();
        
        // If user is a teacher, redirect to teacher courses
        if ($user->isTeacher()) {
            return redirect()->route('teacher.courses');
        }
        
        // For students, show only enrolled courses
        $enrolledCourseIds = \Illuminate\Support\Facades\DB::table('class_student')
            ->where('user_id', $user->id)
            ->pluck('class_id');
        
        $courses = \App\Models\ClassModel::whereIn('id', $enrolledCourseIds)
            ->with('teacher')
            ->withCount(['chapters', 'modules'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // If no enrolled courses, redirect to courses page
        if ($courses->isEmpty()) {
            return redirect()->route('courses')
                ->with('info', 'Anda belum berlangganan course apapun. Silakan pilih course yang ingin Anda ikuti.');
        }
            
        return view('profile.my-courses', compact('courses'));
    }

    public function purchaseHistory()
    {
        $user = auth()->user();
        
        $purchases = Purchase::where('user_id', $user->id)
            ->with('course.teacher')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('profile.purchase-history', compact('purchases'));
    }

    public function invoice($id)
    {
        return view('profile.invoice', compact('id'));
    }

    public function notificationPreferences()
    {
        $user = auth()->user();
        $preferences = $user->getNotificationPreference();
        
        return view('profile.notification-preferences', compact('preferences'));
    }

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

        return redirect()->route('notification-preferences')->with('success', 'Notification preferences updated successfully');
    }
}
