<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!Auth::check() || !($user instanceof User) || !$user->isAdmin()) {
            abort(403);
        }

        return $next($request);
    }
}