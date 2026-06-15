<?php

use App\Http\Middleware\RolePrefix;
use Illuminate\Http\RedirectResponse;

if (!function_exists('role_prefix')) {
    function role_prefix(): string
    {
        $user = auth()->user();

        return $user ? RolePrefix::prefixForUser($user) : 'admin';
    }
}

if (!function_exists('role_route')) {
    /**
     * Generate admin panel route URL with prefix matching the logged-in user's role.
     */
    function role_route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        $path = route($name, $parameters, false);

        $path = preg_replace('#^/admin/#', '/' . role_prefix() . '/', $path, 1);

        return $absolute ? url($path) : $path;
    }
}

if (!function_exists('role_redirect')) {
    function role_redirect(string $name, mixed $parameters = [], int $status = 302): RedirectResponse
    {
        return redirect(role_route($name, $parameters, false), $status);
    }
}
