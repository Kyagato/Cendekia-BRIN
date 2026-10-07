<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            if ($request->expectsJson() || $request->is('api/*')) {
                abort(401, 'Unauthenticated.');
            }
            return redirect('login');
        }

        $user = auth()->user();

        // Super Admin selalu diberikan akses penuh
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Normalisasi role lama ke standar resmi Keycloak
        $userRole = match ($user->role) {
            'Analis Pengetahuan'  => \App\Models\User::ROLE_ANALIS,
            'Kreator Pengetahuan' => \App\Models\User::ROLE_ANGGOTA,
            'Admin IPPD'          => \App\Models\User::ROLE_ADMIN,
            default               => $user->role,
        };

        if (!in_array($userRole, $roles, true)) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini.');
        }

        return $next($request);
    }
}
