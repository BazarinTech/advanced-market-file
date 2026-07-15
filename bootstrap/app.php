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
        // Railway (like Heroku/Render) terminates TLS at its edge and forwards
        // plain HTTP to the container, with X-Forwarded-Proto: https on the
        // original request. Without trusting that proxy, Laravel has no way to
        // know the real request was secure, so url()/asset()/Vite all generate
        // http:// links — which get blocked as mixed content on an https:// page.
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'admin'          => \App\Http\Middleware\AdminMiddleware::class,
            'not.installed'  => \App\Http\Middleware\CheckNotInstalled::class,
            'phone.verified' => \App\Http\Middleware\EnsurePhoneIsVerified::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/callback',
            '/callback/b2c',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
