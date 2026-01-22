<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        // Jika belum login, redirect ke admin login dengan cache control headers
        if (!Auth::check()) {
            return redirect()->route('admin.login')
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
                ])
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman admin.');
        }

        $user = Auth::user();

        // Jika user bukan admin, abort 403
        if (!($user instanceof User) || !$user->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke halaman admin.');
        }

        $response = $next($request);
        
        // Add cache control headers to prevent back button after logout
        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
        
        return $response;
    }
}