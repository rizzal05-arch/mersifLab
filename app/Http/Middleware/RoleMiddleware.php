<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * 
     * Penggunaan:
     * Route::middleware('role:student')->group(function () { ... })
     * Route::middleware('role:teacher')->group(function () { ... })
     * Route::middleware('role:student,teacher')->group(function () { ... })
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Jika tidak ada role yang ditentukan, izinkan akses
        if (empty($roles)) {
            return $next($request);
        }

        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;

        // Cek apakah role user ada di daftar role yang diizinkan
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Jika role tidak sesuai, redirect ke dashboard sesuai role
        if ($userRole === 'teacher') {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        } elseif ($userRole === 'student') {
            return redirect()->route('student.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return redirect()->route('home')
            ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    }
}
