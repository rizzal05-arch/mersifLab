<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Default role untuk user baru
     * Ubah ke 'student' atau 'teacher' sesuai kebutuhan
     */
    private $defaultRole = 'student';

    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Login dengan validasi email verification
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = auth()->user();

            // Cek apakah email sudah diverifikasi
            if (!$user->email_verified_at) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Silakan verifikasi email Anda terlebih dahulu. Periksa inbox atau folder spam Anda.',
                ])->onlyInput('email');
            }

            // Cek banned (terutama untuk teacher)
            if ($user->isBanned()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan (banned). Hubungi admin untuk bantuan.',
                ])->onlyInput('email');
            }

            // Role sesuai, lanjutkan login
            $request->session()->regenerate();
            
            // Update last login untuk tracking
            $user->updateLastLogin();
            
            // Log login activity
            $user->logActivity('user_login', 'User logged in to the system');
            
            // Redirect ke home
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Register user dengan email verification
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Generate verification token
        $verificationToken = Str::random(60);

        // Create user with verification token
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
            'email_verification_token' => $verificationToken,
            'email_verification_sent_at' => now(),
        ]);

        // Send verification email
        $verificationUrl = route('email.verify', [
            'token' => $verificationToken,
            'email' => $user->email,
        ]);

        try {
            Log::info('Sending email verification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'token' => $verificationToken,
                'url' => $verificationUrl,
            ]);
            
            $user->notify(new VerifyEmailNotification($user, $verificationUrl));
            
            Log::info('Email verification sent successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send email verification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        // Redirect to verification pending page
        return redirect()->route('email.verification.pending')
            ->with('email', $user->email)
            ->with('success', 'Email verifikasi telah dikirim. Silakan periksa inbox Anda.');
    }

    /**
     * Show email verification pending page
     */
    public function showVerify()
    {
        return view('auth.email-verification-pending');
    }

    /**
     * Verify email dari link yang dikirim ke email
     */
    public function verifyEmail(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        Log::info('Email verification attempt', [
            'token' => substr($token, 0, 10) . '***',
            'email' => $email,
        ]);

        if (!$token || !$email) {
            Log::warning('Email verification failed: Missing parameters', [
                'has_token' => !!$token,
                'has_email' => !!$email,
            ]);
            return redirect()->route('login')
                ->withErrors(['error' => 'Link verifikasi tidak valid.']);
        }

        // Find user by email and token
        $user = User::where('email', $email)
            ->where('email_verification_token', $token)
            ->first();

        if (!$user) {
            Log::warning('Email verification failed: User or token not found', [
                'email' => $email,
                'token_hint' => substr($token, 0, 10) . '***',
            ]);
            return redirect()->route('login')
                ->withErrors(['error' => 'Link verifikasi tidak valid atau email belum terdaftar.']);
        }

        // Check if token has expired (24 hours)
        if ($user->email_verification_sent_at && $user->email_verification_sent_at->addHours(24)->isPast()) {
            Log::warning('Email verification failed: Token expired', [
                'email' => $email,
                'sent_at' => $user->email_verification_sent_at,
            ]);
            return redirect()->route('email.verification.pending')
                ->withErrors(['error' => 'Link verifikasi telah kadaluarsa. Silakan minta link baru.'])
                ->with('email', $user->email);
        }

        // Mark email as verified
        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'email_verification_sent_at' => null,
        ]);

        Log::info('Email verified successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return redirect()->route('login')
            ->with('success', 'Email berhasil diverifikasi! Silakan login dengan akun Anda.');
    }

    /**
     * Resend verification email
     */
    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $user = User::where('email', $request->email)->first();

        Log::info('Resend verification email attempt', [
            'email' => $user->email,
            'already_verified' => !!$user->email_verified_at,
        ]);

        // Check if email already verified
        if ($user->email_verified_at) {
            return back()->withErrors(['email' => 'Email Anda sudah diverifikasi. Silakan login.']);
        }

        // Generate new verification token
        $verificationToken = Str::random(60);
        
        $user->update([
            'email_verification_token' => $verificationToken,
            'email_verification_sent_at' => now(),
        ]);

        // Send verification email
        $verificationUrl = route('email.verify', [
            'token' => $verificationToken,
            'email' => $user->email,
        ]);

        try {
            Log::info('Resending email verification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'token' => $verificationToken,
            ]);
            
            $user->notify(new VerifyEmailNotification($user, $verificationUrl));
            
            Log::info('Email verification resent successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to resend email verification', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        return back()->with('success', 'Email verifikasi telah dikirim ulang. Silakan periksa inbox Anda.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
