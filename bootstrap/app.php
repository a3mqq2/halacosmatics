<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->prefix('marketer')
                ->name('marketer.')
                ->group(base_path('routes/marketer.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: ['webhook/mosafir']);
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'marketer.active' => \App\Http\Middleware\EnsureMarketerIsActive::class,
        ]);
        $middleware->redirectGuestsTo('/login');
        $middleware->redirectUsersTo(fn(\Illuminate\Http\Request $request) =>
            Auth::guard('marketer')->check() ? '/marketer/dashboard' : '/dashboard'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
