<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RolePrefix
{
    /**
     * Pastikan user mengakses prefix URL yang sesuai dengan role-nya.
     * Jika tidak sesuai, redirect ke URL yang benar.
     */
    public function handle(Request $request, Closure $next, string $expectedPrefix)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $correctPrefix = $this->prefixForUser($user);

        // Jika prefix URL tidak sesuai role, redirect ke URL yang benar
        if ($correctPrefix !== $expectedPrefix) {
            // Ganti prefix di URL saat ini
            $currentPath = $request->path(); // e.g. "admin/orders"
            $segments    = explode('/', $currentPath);
            $segments[0] = $correctPrefix;
            $newPath     = implode('/', $segments);

            $target = '/' . $newPath;

            // Preserve POST/PATCH on redirect (302 would turn them into GET)
            if (!in_array($request->method(), ['GET', 'HEAD'], true)) {
                return redirect($target, 307);
            }

            return redirect($target);
        }

        return $next($request);
    }

    /**
     * Tentukan prefix URL berdasarkan role user.
     */
    public static function prefixForUser($user): string
    {
        if ($user->isOwner()) {
            return 'admin';
        }

        if ($user->isCashier()) {
            return 'kasir';
        }

        if ($user->isKitchen()) {
            return 'dapur';
        }

        return 'admin';
    }
}
