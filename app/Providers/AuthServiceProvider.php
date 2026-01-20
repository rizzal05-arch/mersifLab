<?php

namespace App\Providers;

use App\Models\ClassModel;
use App\Models\Chapter;
use App\Models\Module;
use App\Policies\ContentPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ClassModel::class => ContentPolicy::class,
        Chapter::class => ContentPolicy::class,
        Module::class => ContentPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for content management
        Gate::define('manageContent', function ($user) {
            return $user->isTeacher() || $user->isAdmin();
        });
    }
}
