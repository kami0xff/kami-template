<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust only the local/Docker hops (cloudflared connects from the host
        // over the Docker bridge). Avoid '*', which would let any client spoof
        // X-Forwarded-* headers (IP, proto) if they reach the container directly.
        $middleware->trustProxies(at: [
            '127.0.0.1',
            '10.0.0.0/8',
            '172.16.0.0/12',
            '192.168.0.0/16',
        ]);
        $middleware->alias([
            'set.locale' => \App\Http\Middleware\SetLocale::class,
            'detect.locale' => \App\Http\Middleware\DetectLocale::class,
            'site' => \App\Http\Middleware\SetSite::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Report unhandled exceptions to Sentry (no-op until SENTRY_LARAVEL_DSN
        // is set). The Sentry SDK ships Octane listeners that reset its scope
        // between requests, so this is safe under FrankenPHP worker mode.
        Integration::handles($exceptions);
    })->create();