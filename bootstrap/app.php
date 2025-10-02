<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckTokenExpiration;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Middleware global
        $middleware->use([
            HandleCors::class, // ⚡ Gestion CORS
        ]);

        // Groupes
        $middleware->group('web', [
            SubstituteBindings::class, // ⚡ Si tu gardes des routes web simples
        ]);

        $middleware->group('api', [
            'throttle:api',
            SubstituteBindings::class,
        ]);

        // Alias
        $middleware->alias([
            'auth' => Authenticate::class,
            'admin' => AdminMiddleware::class,
        ]);

        // Middleware custom
        $middleware->append(CheckTokenExpiration::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
