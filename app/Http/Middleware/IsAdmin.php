<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (auth()->guest()) {
            abort(403, 'Anda harus login terlebih dahulu');
        }

        // Cek apakah user adalah admin (role_id = 1)
        if (auth()->user()->role_id != 1) {
            abort(403, 'Anda bukan Admin');
        }

        // Untuk route yang memiliki parameter {id}, cek apakah user mengakses data miliknya sendiri
        if ($request->route()->hasParameter('id')) {
            $routeId = $request->route('id');
            if (auth()->user()->employee_id != $routeId) {
                abort(404);
            }
        }

        return $next($request);
    }
}
