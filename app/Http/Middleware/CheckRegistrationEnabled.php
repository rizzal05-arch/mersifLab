<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;

class CheckRegistrationEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if registration is enabled
        $registrationEnabled = Setting::get('registration_enabled', '1') === '1';
        
        // If registration is disabled and user is trying to register
        if (!$registrationEnabled && $request->is('register') && $request->isMethod('get')) {
            return redirect()->route('login')->with('error', 'Registration is currently disabled. Please contact administrator.');
        }

        // If registration is disabled and user is trying to submit registration
        if (!$registrationEnabled && $request->is('register') && $request->isMethod('post')) {
            return redirect()->route('login')->with('error', 'Registration is currently disabled. Please contact administrator.');
        }

        return $next($request);
    }
}
