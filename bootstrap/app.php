<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )


    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'Image' => Intervention\Image\Facades\Image::class,
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
        ]);
    })

    ->withSchedule(function ($schedule) {
        $schedule->command('businesses:mark-expired-inactive')
            ->daily()
            ->at('02:00'); // Run daily at 2 AM
    })


    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
