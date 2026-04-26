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
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
    ]);

    $middleware->redirectTo(
        guests: '/login',
        users: function ($request) {
            if ($request->is('admin/*')) return route('admin.dashboard');
            if ($request->is('teacher/*')) return route('teacher.dashboard');
            if ($request->is('parent/*')) return route('parent.dashboard');
            return '/';
        }
    );
})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();