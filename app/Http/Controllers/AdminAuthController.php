<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Proses login (POST)
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Username harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        // Attempt login menggunakan email atau username
        // Jika menggunakan email sebagai username
        if (Auth::attempt(['email' => $credentials['username'], 'password' => $credentials['password']], $request->remember)) {
            // Regenerate session ID untuk keamanan
            $request->session()->regenerate();

            // Redirect ke admin dashboard
            return redirect()->route('admin.dashboard')->with('success', 'Login berhasil!');
        }

        // Jika login gagal
        return redirect()->back()
            ->withInput($request->only('username'))
            ->with('error', 'Email atau Password salah');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah logout');
    }
}
