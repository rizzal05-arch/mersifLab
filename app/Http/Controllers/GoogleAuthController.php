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
        $requestedRole = Session::pull('google_role', 'student');

        // Check if user exists by email
        $existingUser = User::where('email', $googleUser->email)->first();

        // If user exists, check role compatibility
        if ($existingUser) {
            // If user has different role, reject this login attempt
            if ($existingUser->role !== $requestedRole) {
                $roleText = $existingUser->role === 'student' ? 'Student' : 'Teacher';
                return redirect('/login')->with('error', 
                    "Akun Google ini sudah terdaftar sebagai $roleText. Anda tidak dapat login dengan role berbeda."
                );
            }

            // Update google_id if not set
            if (!$existingUser->google_id) {
                $existingUser->update(['google_id' => $googleUser->id]);
            }

            $user = $existingUser;
        } else {
            // Check if user exists by google_id (in case google_id was set from another registration)
            $user = User::where('google_id', $googleUser->id)->first();

            if (!$user) {
                // Create new user with role from session or default = student
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => null,
                    'role' => $requestedRole,
                    'is_subscriber' => false,
                ]);
            }
        }

        // Cek banned sebelum login
        if ($user->isBanned()) {
            return redirect('/login')->with('error', 'Akun Anda telah dinonaktifkan (banned). Hubungi admin untuk bantuan.');
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
