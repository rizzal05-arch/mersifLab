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

        // Check if user exists by email OR google_id (satu akun Google = satu role)
        $existingUserByEmail = User::where('email', $googleUser->email)->first();
        $existingUserByGoogleId = User::where('google_id', $googleUser->id)->first();
        
        // Prioritize user yang ditemukan (email lebih utama karena lebih unik)
        $existingUser = $existingUserByEmail ?? $existingUserByGoogleId;

        // If user exists, check role compatibility
        if ($existingUser) {
            // Jika user sudah punya role yang berbeda, tolak login
            if ($existingUser->role !== $requestedRole) {
                $roleText = $existingUser->role === 'student' ? 'Student' : 'Teacher';
                $requestedRoleText = $requestedRole === 'student' ? 'Student' : 'Teacher';
                
                return redirect('/login')
                    ->with('error', 
                        "Akun Google ini sudah terdaftar sebagai $roleText. Anda tidak dapat login sebagai $requestedRoleText. Silakan login dengan tab yang sesuai."
                    )
                    ->with('active_tab', $existingUser->role);
            }

            // Update google_id dan email jika belum set atau berbeda
            try {
                $updateData = [];
                if (!$existingUser->google_id || $existingUser->google_id !== $googleUser->id) {
                    $updateData['google_id'] = $googleUser->id;
                }
                if ($existingUser->email !== $googleUser->email) {
                    // Jika email berbeda, pastikan tidak ada user lain dengan email Google ini
                    $emailConflict = User::where('email', $googleUser->email)
                        ->where('id', '!=', $existingUser->id)
                        ->first();
                    
                    if ($emailConflict) {
                        return redirect('/login')->with('error', 
                            'Email Google ini sudah digunakan oleh akun lain. Satu akun Google hanya bisa memiliki satu role.'
                        );
                    }
                    
                    $updateData['email'] = $googleUser->email;
                }
                
                if (!empty($updateData)) {
                    $existingUser->update($updateData);
                }
            } catch (\Exception $e) {
                \Log::error('Google Auth Update Error: ' . $e->getMessage());
                // Continue dengan login meskipun update gagal
            }

            $user = $existingUser;
        } else {
            // User baru - buat dengan role yang diminta
            // Pastikan tidak ada user lain dengan email atau google_id yang sama
            $emailExists = User::where('email', $googleUser->email)->exists();
            $googleIdExists = User::where('google_id', $googleUser->id)->exists();
            
            if ($emailExists || $googleIdExists) {
                return redirect('/login')->with('error', 
                    'Akun Google ini sudah terdaftar. Silakan login dengan email dan password atau gunakan tab yang sesuai.'
                );
            }
            
            // Create new user with role from session
            try {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => null,
                    'role' => $requestedRole,
                    'is_subscriber' => false,
                ]);
            } catch (\Exception $e) {
                \Log::error('Google Auth Create User Error: ' . $e->getMessage());
                return redirect('/login')->with('error', 
                    'Gagal membuat akun baru. Silakan coba lagi atau hubungi admin.'
                );
            }
        }

        // Cek banned sebelum login
        if ($user->isBanned()) {
            return redirect('/login')->with('error', 'Akun Anda telah dinonaktifkan (banned). Hubungi admin untuk bantuan.');
        }

        // Login user
        Auth::login($user, remember: true);

        // Update last login untuk tracking (sama seperti login biasa)
        $user->updateLastLogin();
        
        // Log login activity (sama seperti login biasa)
        $user->logActivity('google_login', 'User logged in to the system via Google');

        // Clear ALL error-related sessions before successful redirect
        // This ensures no error popup appears after successful login
        Session::forget('error');
        Session::forget('active_tab');
        Session::forget('google_role');
        
        // Also remove error from flash data if exists
        Session::remove('error');

        // Redirect based on user role - use intended() to avoid redirecting back to login
        // Use with() to set success message, but ensure error is not included
        $redirect = null;
        if ($user->isTeacher()) {
            // Redirect teachers to home page (consistent with password login flow)
            $redirect = redirect()->intended(route('home'));
        } elseif ($user->isStudent()) {
            $redirect = redirect()->intended(route('home'));
        } else {
            $redirect = redirect()->intended('/');
        }
        
        // Set success message and ensure error is cleared
        return $redirect->with('success', 'Login berhasil!')->without('error');
    }
}
