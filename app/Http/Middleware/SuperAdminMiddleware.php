<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && (auth()->user()->role === 'Super Admin' || auth()->user()->email === 'superadmin@brin.go.id')) {
            return $next($request);
        }
        
        abort(403, 'Akses ditolak. Fitur ini hanya untuk Super Admin.');
    }
}
