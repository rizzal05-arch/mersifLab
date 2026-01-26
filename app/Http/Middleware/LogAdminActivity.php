<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class LogAdminActivity
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
        
        // Only log if user is authenticated and is admin
        if (Auth::check() && Auth::user()->isAdmin()) {
            $this->logActivity($request);
        }
        
        return $response;
    }

    /**
     * Log the admin activity
     */
    private function logActivity(Request $request): void
    {
        $method = $request->method();
        $route = $request->route();
        
        if (!$route) {
            return;
        }

        $routeName = $route->getName();
        
        // Define which routes to log
        $loggableRoutes = [
            'admin.admins.store',
            'admin.admins.update',
            'admin.admins.destroy',
            'admin.courses.store',
            'admin.courses.update',
            'admin.courses.destroy',
            'admin.teachers.store',
            'admin.teachers.update',
            'admin.teachers.destroy',
            'admin.students.store',
            'admin.students.update',
            'admin.students.destroy',
        ];

        if (!in_array($routeName, $loggableRoutes)) {
            return;
        }

        $action = $this->getActionFromRoute($routeName, $method);
        $description = $this->getDescriptionFromRequest($request, $routeName, $method);

        if ($action && $description) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'description' => $description,
            ]);
        }
    }

    /**
     * Get action name from route and method
     */
    private function getActionFromRoute(string $routeName, string $method): ?string
    {
        $resource = explode('.', $routeName)[1] ?? null;
        
        if (!$resource) {
            return null;
        }

        switch ($method) {
            case 'POST':
                return $resource . '_created';
            case 'PUT':
            case 'PATCH':
                return $resource . '_updated';
            case 'DELETE':
                return $resource . '_deleted';
            default:
                return null;
        }
    }

    /**
     * Get description from request
     */
    private function getDescriptionFromRequest(Request $request, string $routeName, string $method): ?string
    {
        $resource = explode('.', $routeName)[1] ?? null;
        $user = Auth::user();

        switch ($method) {
            case 'POST':
                if ($resource === 'admins') {
                    $name = $request->input('name');
                    $email = $request->input('email');
                    return "Created new admin: {$name} ({$email})";
                }
                break;
                
            case 'PUT':
            case 'PATCH':
                if ($resource === 'admins') {
                    $admin = $request->route('admin');
                    if ($admin) {
                        return "Updated admin: {$admin->name} ({$admin->email})";
                    }
                }
                break;
                
            case 'DELETE':
                if ($resource === 'admins') {
                    $adminId = $request->route('admin');
                    // Note: Admin is already deleted, so we can't get the name
                    return "Deleted admin with ID: {$adminId}";
                }
                break;
        }

        return null;
    }
}
