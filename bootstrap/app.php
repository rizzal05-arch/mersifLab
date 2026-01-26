<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'maintenance' => \App\Http\Middleware\CheckMaintenanceMode::class,
            'registration.enabled' => \App\Http\Middleware\CheckRegistrationEnabled::class,
            'log.admin' => \App\Http\Middleware\LogAdminActivity::class,
            'update.login' => \App\Http\Middleware\UpdateLastLogin::class,
            'ajax.handler' => \App\Http\Middleware\HandleAjaxRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
