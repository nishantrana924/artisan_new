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
        $trusted = env('TRUSTED_PROXIES');
        if (!empty($trusted)) {
            $proxies = array_filter(array_map('trim', explode(',', $trusted)));
            if (!empty($proxies)) {
                $middleware->trustProxies(at: count($proxies) === 1 && $proxies[0] === '*' ? '*' : $proxies);
            }
        }
        $middleware->validateCsrfTokens(except: [
            'webhook/whatsapp',
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'message' => 'CSRF token mismatch or session expired. Please refresh and try again.',
                ], 419);
            }

            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation', '_token', 'cf-turnstile-response'))
                ->with('error', 'Your page session expired. Please log in again.');
        });
    })->create();
