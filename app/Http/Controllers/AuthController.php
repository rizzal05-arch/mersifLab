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
     * Login dengan validasi role dan redirect berdasarkan role
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:student,teacher',
        ]);

        $role = $credentials['role'];
        unset($credentials['role']);

        if (Auth::attempt($credentials)) {
            $user = auth()->user();

            // Cek banned (terutama untuk teacher)
            if ($user->isBanned()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun Anda telah dinonaktifkan (banned). Hubungi admin untuk bantuan.',
                ])->with('active_tab', $role)->onlyInput('email');
            }
            
            // Validasi role user sesuai dengan tab login yang dipilih
            if ($role === 'teacher' && !$user->isTeacher()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Anda bukan seorang guru. Silakan login di halaman Student.',
                ])->with('error_type', 'wrong_role_teacher')->with('active_tab', 'student')->onlyInput('email');
            }
            
            if ($role === 'student' && !$user->isStudent()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Anda bukan seorang siswa. Silakan login di halaman Teacher.',
                ])->with('error_type', 'wrong_role_student')->with('active_tab', 'teacher')->onlyInput('email');
            }

            // Role sesuai, lanjutkan login
            $request->session()->regenerate();
            
            // Redirect berdasarkan role user
            if ($user->isTeacher()) {
                return redirect()->route('home');
            } 
            
            // Default fallback
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->with('active_tab', $role)->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Register user dengan role dari form (student atau teacher)
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:student,teacher',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'], // Ambil role dari form
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
