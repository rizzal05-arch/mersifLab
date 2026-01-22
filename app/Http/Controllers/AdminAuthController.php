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
        // Jika sudah login sebagai admin, redirect ke dashboard
        if (auth()->check() && auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        $response = response()->view('admin.auth.login');
        
        // Add cache control headers
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        
        return $response;
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
        // Check remember me checkbox (Laravel expects boolean)
        $remember = $request->has('remember') && ($request->remember == '1' || $request->remember === true || $request->remember === 'on');
        
        // Jika menggunakan email sebagai username
        if (Auth::attempt(['email' => $credentials['username'], 'password' => $credentials['password']], $remember)) {
            $user = Auth::user();
            
            // Cek apakah user adalah admin
            if (!$user->isAdmin()) {
                Auth::logout();
                return redirect()->back()
                    ->withInput($request->only('username'))
                    ->with('error', 'Anda bukan administrator. Silakan login di halaman yang sesuai.');
            }
            
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

        // Redirect ke admin login dengan cache control headers untuk prevent back button
        return redirect()->route('admin.login')
            ->withHeaders([
                'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
            ])
            ->with('success', 'Anda telah logout');
    }
}
