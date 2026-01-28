<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Course;
use App\Models\Setting;
use App\Policies\CoursePolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Policies
        // $this->registerPolicies();

        // Alternative: Direct policy registration
        // Gate::policy(Course::class, CoursePolicy::class);

        // Listen to Auth events for activity logging
        Event::listen(Login::class, function (Login $event) {
            $user = $event->user;
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'login',
                'description' => "User logged in: {$user->name} ({$user->email})",
                'subject_type' => get_class($user),
                'subject_id' => $user->id,
            ]);
        });

        Event::listen(Logout::class, function (Logout $event) {
            $user = $event->user;
            if ($user) {
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'logout',
                    'description' => "User logged out: {$user->name} ({$user->email})",
                    'subject_type' => get_class($user),
                    'subject_id' => $user->id,
                ]);
            }
        });

        // Share site logo and favicon to all views
        View::composer('*', function ($view) {
            // Logo
            $logoPath = Setting::get('site_logo', 'images/logo.png');
            
            // Check if logo is in storage
            if (Storage::disk('public')->exists($logoPath)) {
                $logoUrl = Storage::url($logoPath);
            } elseif (file_exists(public_path($logoPath))) {
                // Fallback to public path
                $logoUrl = asset($logoPath);
            } else {
                // Default logo
                $logoUrl = asset('images/logo.png');
            }
            
            // Favicon
            $faviconPath = Setting::get('site_favicon', 'images/favicon.png');
            
            // Check if favicon is in storage
            if (Storage::disk('public')->exists($faviconPath)) {
                $faviconUrl = Storage::url($faviconPath);
            } elseif (file_exists(public_path($faviconPath))) {
                // Fallback to public path
                $faviconUrl = asset($faviconPath);
            } else {
                // Default favicon
                $faviconUrl = asset('images/favicon.png');
            }
            
            $view->with('siteLogoUrl', $logoUrl);
            $view->with('siteFaviconUrl', $faviconUrl);
        });
    }
}
