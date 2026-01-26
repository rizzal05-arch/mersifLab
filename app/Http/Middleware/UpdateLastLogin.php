<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateLastLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Update last login only for successful admin login
        if (Auth::check() && Auth::user()->isAdmin()) {
            // Only update if this is a login request (check for specific conditions)
            if ($this->isLoginRequest($request)) {
                Auth::user()->updateLastLogin();
                
                // Log the login activity
                Auth::user()->logActivity('admin_login', 'Admin logged in to the system');
            }
        }
        
        return $response;
    }

    /**
     * Check if this is a login request
     */
    private function isLoginRequest(Request $request): bool
    {
        // Check if the request is for admin login and was successful
        return $request->is('admin/login') && $request->isMethod('POST');
    }
}
