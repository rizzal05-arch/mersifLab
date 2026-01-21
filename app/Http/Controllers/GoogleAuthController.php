<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google for authentication
     */
    public function redirect($role = null)
    {
        // Store role in session if provided (for registration flow)
        if ($role && in_array($role, ['student', 'teacher'])) {
            Session::put('google_role', $role);
        }
        
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            \Log::error('Google Auth Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Google login failed: ' . $e->getMessage());
        }

        // Get role from session if available
        $role = Session::pull('google_role', 'student');

        // Check if user exists by google_id
        $user = User::where('google_id', $googleUser->id)->first();

        // If user doesn't exist, create new user
        if (!$user) {
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Create new user with role from session or default = student
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => null,
                    'role' => $role,
                    'is_subscriber' => false,
                ]);
            } else {
                // Update existing user with google_id
                $user->update(['google_id' => $googleUser->id]);
            }
        }

        // Login user
        Auth::login($user, remember: true);

        // Redirect based on user role
        if ($user->isTeacher()) {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->isStudent()) {
            return redirect()->route('home');
        }

        return redirect('/');
    }
}
