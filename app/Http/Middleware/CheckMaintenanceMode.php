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
        // Check if maintenance mode is enabled
        $maintenanceMode = Setting::get('maintenance_mode', '0') === '1';
        
        if ($maintenanceMode) {
            // Allow admin to access even during maintenance
            if (auth()->check() && auth()->user()->isAdmin()) {
                return $next($request);
            }

            // Allow admin routes for admin login
            if ($request->is('admin/*', 'admin')) {
                return $next($request);
            }

            // Block all other access during maintenance
            // This includes login, register, about, and all public pages
            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }
}
