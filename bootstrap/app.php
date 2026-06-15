<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'customer.session' => \App\Http\Middleware\CustomerSession::class,
            'permission'       => \App\Http\Middleware\CheckPermission::class,
            'role.prefix'      => \App\Http\Middleware\RolePrefix::class,
        ]);

        RedirectIfAuthenticated::redirectUsing(function (Request $request): string {
            $user = $request->user();
            if ($user) {
                $prefix = \App\Http\Middleware\RolePrefix::prefixForUser($user);
                if ($user->isKitchen()) {
                    return url($prefix . '/orders');
                }
                if ($user->hasPermission('view_dashboard')) {
                    return url($prefix . '/dashboard');
                }
                return url($prefix . '/orders');
            }
            return route('admin.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
