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
    ->withCommands([
        \App\Console\Commands\LogoutAllUsers::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'active.user' => \App\Http\Middleware\ActiveUser::class,
            'require.pin' => \App\Http\Middleware\RequirePin::class,
            'check.blocked.ip' => \App\Http\Middleware\CheckBlockedIp::class,
            'super.admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
