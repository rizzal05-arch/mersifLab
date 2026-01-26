<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow admin to access even during maintenance
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // Allow login and admin routes
        if ($request->is('login*', 'admin/*', 'register*', 'verify*')) {
            return $next($request);
        }

        // Check if maintenance mode is enabled
        $maintenanceMode = Setting::get('maintenance_mode', '0') === '1';
        
        if ($maintenanceMode) {
            // Show maintenance page for non-admin users
            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }
}
