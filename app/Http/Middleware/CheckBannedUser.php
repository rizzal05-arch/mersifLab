<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckBannedUser
{
    /**
     * Handle an incoming request.
     * Check if authenticated user is banned and logout immediately if banned.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Only check for authenticated users
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user is banned (for teachers and students)
            if ($user->isBanned()) {
                // Invalidate the current session
                Auth::logout();
                Session::invalidate();
                Session::regenerateToken();
                
                // Redirect to login with banned message
                if ($request->is('admin/*')) {
                    return redirect()->route('admin.login')
                        ->with('error', 'Your account has been banned by the administrator. Please contact support for assistance.');
                }
                
                return redirect()->route('login')
                    ->with('error', 'Your account has been banned by the administrator. Please contact support for assistance.');
            }
        }
        
        $response = $next($request);

        // Add headers to prevent caching for authenticated users
        if (Auth::check()) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
