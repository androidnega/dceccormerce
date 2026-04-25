<?php

use App\Http\Middleware\CustomerMiddleware;
use App\Http\Middleware\EnsureAdminRole;
use App\Http\Middleware\EnsureManagerRole;
use App\Http\Middleware\RiderMiddleware;
use App\Http\Middleware\StaffMiddleware;
use App\Http\Middleware\TrustLocalLanUrls;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            TrustLocalLanUrls::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'paystack/webhook',
        ]);

        $middleware->alias([
            'staff' => StaffMiddleware::class,
            'admin' => EnsureAdminRole::class,
            'manager' => EnsureManagerRole::class,
            'customer' => CustomerMiddleware::class,
            'rider' => RiderMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));

        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();
            if (! $user) {
                return route('account.index');
            }

            if (in_array($user->role, ['admin', 'manager'], true)) {
                return route('dashboard.index');
            }

            if ($user->role === 'rider') {
                return route('rider.dashboard');
            }

            return route('account.index');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
