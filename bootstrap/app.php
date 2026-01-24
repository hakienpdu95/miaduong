<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__.'/../routes/web.php',
            __DIR__.'/../routes/backend.php',
        ],
        api: [
            __DIR__.'/../routes/api.php', // API routes
        ],
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [

        ]);

        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'check.permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withProviders([
        \App\Providers\ViewServiceProvider::class,
        \App\Services\ModuleServiceProvider::class, 
        \App\Providers\MorphMapServiceProvider::class,
    ])
    ->withSingletons([
        \App\Services\BreadcrumbService::class,
    ])
    ->create();
