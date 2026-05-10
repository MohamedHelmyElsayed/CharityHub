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
        // Prevent browser back-button from showing cached authenticated pages after logout
        $middleware->append(\App\Http\Middleware\PreventBackHistory::class);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'idempotency' => \App\Http\Middleware\HandleIdempotency::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'stripe/webhook',
            'webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
