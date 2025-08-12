<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployeeAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Cek jika user tidak memiliki role_id = 3 (karyawan)
        if ($user->role_id != 3) {
            return response()->json([
                'message' => 'Akses ditolak. Anda bukan karyawan'
            ], 403);
        }
        
        // Dapatkan ID dari URL
        $requestedId = $request->route('id');
        
        // Jika user mencoba mengakses data yang bukan miliknya
        if ($user->employee_id != $requestedId) {
            return response()->json([
                'message' => 'Akses ditolak. Anda hanya dapat mengakses data Anda sendiri.'
            ], 403);
        }
        
        return $next($request);
    }
}