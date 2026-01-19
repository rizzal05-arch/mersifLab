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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role = null): Response
    {
        // If no role is specified, allow access
        if (!$role) {
            return $next($request);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user has the required role
        $userRole = auth()->user()->role ?? 'user';
        
        if ($userRole === $role) {
            return $next($request);
        }

        // If user doesn't have the required role, redirect to home
        return redirect()->route('home')->with('error', 'Unauthorized access');
    }
}
