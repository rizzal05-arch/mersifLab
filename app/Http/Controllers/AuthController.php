<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
     * Login dengan validasi sederhana tanpa role
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = auth()->user();

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
     * Register user dengan role otomatis sebagai student
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student', // Otomatis set sebagai student
        ]);

        return redirect('/login')->with('success', 'Registration successful. Please login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
