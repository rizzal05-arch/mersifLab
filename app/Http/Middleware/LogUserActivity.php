<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     * Track user activity using Cache for real-time online status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Track user activity for all authenticated users
        if (Auth::check()) {
            $user = Auth::user();
            
            // Store user online status in cache (expires in 2 minutes)
            // This gives a 1-minute buffer for the 1-minute requirement
            Cache::put('user-is-online-' . $user->id, true, now()->addMinutes(2));
            
            // Optionally update last_seen_at in database for persistent history
            // Uncomment if you have a last_seen_at column
            // $user->update(['last_seen_at' => now()]);
        }
        
        return $response;
    }
}
