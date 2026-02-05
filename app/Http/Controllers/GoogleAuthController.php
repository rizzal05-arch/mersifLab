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
        // Always set role as student
        Session::put('google_role', 'student');
        Session::save();
        
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            // Common intermittent issue: session/state not available on callback.
            // Retry with a stateless request as a defensive fallback and log a warning.
            \Log::warning('Google Auth InvalidState — retrying stateless fallback: ' . $e->getMessage());
            try {
                $googleUser = Socialite::driver('google')->stateless()->user();
            } catch (\Exception $e2) {
                \Log::error('Google Auth Stateless fallback failed: ' . $e2->getMessage());
                return redirect('/login')->with('error', 'Google login failed (session mismatch). Silakan coba lagi.');
            }
        } catch (\Exception $e) {
            \Log::error('Google Auth Error: ' . $e->getMessage());
            $msg = $e->getMessage() ?: 'Unknown error during Google authentication. Please try again.';
            return redirect('/login')->with('error', 'Google login failed: ' . $msg);
        }

        // Get role from session (always student)
        $requestedRole = 'student';

        // Check if user exists by email OR google_id
        $existingUserByEmail = User::where('email', $googleUser->email)->first();
        $existingUserByGoogleId = User::where('google_id', $googleUser->id)->first();
        
        // Prioritize user yang ditemukan (email lebih utama karena lebih unik)
        $existingUser = $existingUserByEmail ?? $existingUserByGoogleId;

        // If user exists, login directly
        if ($existingUser) {
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
            // User baru - buat dengan role student
            // Pastikan tidak ada user lain dengan email atau google_id yang sama
            $emailExists = User::where('email', $googleUser->email)->exists();
            $googleIdExists = User::where('google_id', $googleUser->id)->exists();
            
            if ($emailExists || $googleIdExists) {
                return redirect('/login')->with('error', 
                    'Akun Google ini sudah terdaftar. Silakan login dengan email dan password.'
                );
            }
            
            // Create new user with student role
            try {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => null,
                    'role' => 'student',
                    'is_subscriber' => false,
                ]);
            } catch (\Exception $e) {
                \Log::error('Google Auth Create User Error: ' . $e->getMessage());
                return redirect('/login')->with('error', 
                    'Gagal membuat akun baru. Silakan coba lagi atau hubungi admin.'
                );
            }
        }

        // Import Google avatar if user has none and Google provides one
        try {
            if ((empty($user->avatar) || !$user->avatar) && !empty($googleUser->avatar)) {
                // Try to fetch avatar
                $avatarUrl = $googleUser->avatar;
                // Prefer a larger size if Google provides sz param
                if (strpos($avatarUrl, 'sz=') === false) {
                    $avatarUrl = $avatarUrl . (strpos($avatarUrl, '?') === false ? '?' : '&') . 'sz=512';
                }

                $response = \Illuminate\Support\Facades\Http::get($avatarUrl);

                if ($response->successful()) {
                    $content = $response->body();
                    // Limit to 2MB like regular uploads
                    if (strlen($content) <= 2 * 1024 * 1024) {
                        $contentType = $response->header('Content-Type', 'image/jpeg');
                        $ext = 'jpg';
                        if (strpos($contentType, 'png') !== false) $ext = 'png';
                        if (strpos($contentType, 'gif') !== false) $ext = 'gif';
                        if (strpos($contentType, 'webp') !== false) $ext = 'webp';

                        $filename = 'avatars/google_' . $user->id . '_' . time() . '.' . $ext;

                        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $content);

                        // Save avatar path on user
                        $user->avatar = $filename;
                        $user->save();
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to import Google avatar for user ' . ($user->id ?? 'unknown') . ': ' . $e->getMessage());
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

        // Clear ALL error-related sessions BEFORE redirect to ensure clean state
        // This ensures no error popup appears after successful login
        Session::flush();

        // Redirect based on user role - always send to home to avoid unintended redirection back to login
        // Use ->to() to force the destination
        $redirect = redirect()->to(route('home'));

        // Set success message (yang penting adalah tidak ada error)
        return $redirect->with('success', 'Login berhasil!');
    }
}
