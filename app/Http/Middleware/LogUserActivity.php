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
        // Track user activity BEFORE processing request to ensure it runs early
        // This ensures the cache is updated as soon as possible
        if (Auth::check()) {
            $user = Auth::user();
            
            // Store user online status in cache (expires in 2 minutes)
            // This gives a 1-minute buffer for the 1-minute requirement
            try {
                Cache::put('user-is-online-' . $user->id, true, now()->addMinutes(2));
            } catch (\Exception $e) {
                // If cache fails, log error but don't break the request
                \Log::warning('Failed to update user online status in cache: ' . $e->getMessage());
            }
        }
        
        $response = $next($request);
        
        return $response;
    }
}
