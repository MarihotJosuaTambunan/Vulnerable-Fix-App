<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Cek jika user tidak memiliki role_id = 1 (Admin)
        if ($user->role_id != 1) {
            return response()->json([
                'message' => 'Akses ditolak. Anda bukan Admin'
            ], 403);
        }
        return $next($request);
    }
}