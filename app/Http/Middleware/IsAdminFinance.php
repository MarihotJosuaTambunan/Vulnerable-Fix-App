<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdminFinance
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

        // Dapatkan role_id user
        $roleId = auth()->user()->role_id;

        // Hanya admin (1) dan finance_manager (2) yang boleh akses
        if (!in_array($roleId, [1, 2])) {
            abort(403, 'Akses hanya untuk Admin dan Finance Manager');
        }

        return $next($request);
    }
}