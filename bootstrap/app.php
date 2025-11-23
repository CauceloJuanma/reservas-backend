<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ✅ 1. Alias (correcto, no tocar)
        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

        // ✅ 2. CORS al PRINCIPIO del grupo API
        // Razón: Debe ejecutarse ANTES que cualquier otro middleware
        // para agregar los headers necesarios ANTES de validaciones
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        // ✅ 3. Sanctum stateful para SPAs
        // Esto automáticamente agrega EnsureFrontendRequestsAreStateful
        // al grupo API (NO al web)
        $middleware->statefulApi();

        // ✅ 4. Excluir CSRF de rutas API
        // Razón: Las APIs usan tokens en headers, no CSRF tokens
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
