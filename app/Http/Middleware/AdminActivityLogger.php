<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminActivityLogger
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
        
        // Log admin activities for specific routes
        if (Auth::check() && Auth::user()->isAdmin()) {
            $this->logAdminActivity($request);
        }
        
        return $response;
    }
    
    /**
     * Log admin activities based on request
     */
    private function logAdminActivity(Request $request): void
    {
        $user = Auth::user();
        $route = $request->route();
        
        if (!$route) return;
        
        $routeName = $route->getName();
        $method = $request->method();
        
        // Define activities to log
        $activities = [
            // Admin activities
            'admin.admins.index' => 'Viewed admin management list',
            'admin.admins.show' => 'Viewed admin details',
            'admin.admins.create' => 'Accessed admin creation form',
            'admin.admins.store' => 'Created new admin',
            'admin.admins.edit' => 'Accessed admin edit form',
            'admin.admins.update' => 'Updated admin information',
            'admin.admins.destroy' => 'Deleted admin',
            'admin.admins.toggleStatus' => 'Toggled admin status',
            
            // Student activities
            'admin.students.index' => 'Viewed student management list',
            'admin.students.show' => 'Viewed student details',
            'admin.students.activities' => 'Viewed student activities',
            'admin.students.create' => 'Accessed student creation form',
            'admin.students.store' => 'Created new student',
            'admin.students.edit' => 'Accessed student edit form',
            'admin.students.update' => 'Updated student information',
            'admin.students.destroy' => 'Deleted student',
            'admin.students.toggleBan' => 'Toggled student ban status',
            
            // Teacher activities
            'admin.teachers.index' => 'Viewed teacher management list',
            'admin.teachers.show' => 'Viewed teacher details',
            'admin.teachers.create' => 'Accessed teacher creation form',
            'admin.teachers.store' => 'Created new teacher',
            'admin.teachers.edit' => 'Accessed teacher edit form',
            'admin.teachers.update' => 'Updated teacher information',
            'admin.teachers.destroy' => 'Deleted teacher',
            'admin.teachers.toggleBan' => 'Toggled teacher ban status',
            
            // System activities
            'admin.dashboard' => 'Accessed admin dashboard',
            'admin.settings.index' => 'Viewed system settings',
            'admin.settings.update' => 'Updated system settings',
            'admin.settings.uploadLogo' => 'Uploaded system logo',
            'admin.messages.index' => 'Viewed messages list',
            'admin.messages.show' => 'Viewed message details',
        ];
        
        if (isset($activities[$routeName])) {
            $description = $activities[$routeName];
            
            // Add additional context for specific actions
            if ($method === 'POST') {
                $description .= ' (POST request)';
            } elseif ($method === 'PUT' || $method === 'PATCH') {
                $description .= ' (update request)';
            } elseif ($method === 'DELETE') {
                $description .= ' (delete request)';
            }
            
            $user->logActivity('admin_activity', $description);
        }
    }
}
