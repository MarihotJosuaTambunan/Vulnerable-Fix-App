<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeApiController;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Debt;
use App\Http\Controllers\AuthController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware(['auth:sanctum', 'employee.access'])->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Employee profile routes
    Route::get('/dashboard/karyawan/profile/{id}', function($id) {
        try {
            $employee = Employee::find($id);
            
            if(!$employee) {
                return response()->json(['message' => 'Data karyawan tidak ditemukan'], 404);
            }
            
            $formattedEmployee = [
                'id' => $employee->id,
                'nama' => $employee->nama,
                'nip' => $employee->nip,
                'jenis_kelamin' => $employee->jenis_kelamin,
                'tempat_lahir' => $employee->tempat_lahir,
                'tgl_lahir' => $employee->tgl_lahir,
                'alamat' => $employee->alamat,
                'no_telp' => $employee->no_telp,
                'no_rek' => $employee->no_rek,
                'bank' => $employee->bank,
                'tgl_masuk' => $employee->tgl_masuk,
                'status' => $employee->status,
            ];
            
            return response()->json([
                'data' => $formattedEmployee,
                'message' => 'Data profile karyawan berhasil diambil'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data profile',
                'error' => $e->getMessage()
            ], 500);
        }
    });

    // Employee salary routes
    Route::get('dashboard/karyawan/gaji/{id}', function($id) {
        try {
            $salaries = EmployeeSalary::with(['salary'])
                ->where('karyawan_id', $id)
                ->get(['id', 'karyawan_id', 'gaji_id', 'tgl_gajian', 'created_at', 'updated_at']);
                
            if($salaries->isEmpty()) {
                return response()->json(['message' => 'Data gaji tidak ditemukan'], 404);
            }
            
            $formattedSalaries = $salaries->map(function($item) {
                return [
                    'id' => $item->id,
                    'tgl_gajian' => $item->tgl_gajian,
                    'jabatan' => $item->salary->jabatan ?? 'Tidak diketahui',
                    'gaji_pokok' => $item->salary->gaji_pokok ?? 0,
                    'tunjangan_transport' => $item->salary->tj_transport ?? 0,
                    'uang_makan' => $item->salary->uang_makan ?? 0,
                    'total_gaji' => ($item->salary->gaji_pokok ?? 0) + 
                                    ($item->salary->tj_transport ?? 0) + 
                                    ($item->salary->uang_makan ?? 0)
                ];
            });
            
            $total = $formattedSalaries->sum('total_gaji');
            
            return response()->json([
                'data' => $formattedSalaries,
                'total_keseluruhan' => $total,
                'message' => 'Data gaji karyawan berhasil diambil'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data gaji',
                'error' => $e->getMessage()
            ], 500);
        }
    });

    // Employee dashboard route
    Route::get('/dashboard/karyawan/{id}', function($id) {
        try {
            $employee = Employee::with('salary')->find($id);
            
            if(!$employee) {
                return response()->json(['message' => 'Data karyawan tidak ditemukan'], 404);
            }
            
            $salaries = EmployeeSalary::where('karyawan_id', $id)
                ->with('salary')
                ->get();
                
            $totalGaji = $salaries->sum(function($item) {
                return $item->salary->gaji_pokok + $item->salary->tj_transport + $item->salary->uang_makan;
            });
            
            $debts = Debt::where('employee_id', $id)->get();
            $hutangBelumLunas = $debts->where('keterangan', 'Belum Lunas')
                                    ->where('status', 1)
                                    ->sum('jumlah_hutang');
            
            return response()->json([
                'data' => [
                    'employee' => [
                        'id' => $employee->id,
                        'nama' => $employee->nama,
                        'jabatan' => $employee->salary->jabatan ?? 'Tidak diketahui',
                        'gaji_pokok' => $employee->salary->gaji_pokok ?? 0,
                        'tunjangan_transport' => $employee->salary->tj_transport ?? 0,
                        'uang_makan' => $employee->salary->uang_makan ?? 0
                    ],
                    'total_gaji' => $totalGaji,
                    'hutang_belum_lunas' => $hutangBelumLunas,
                    'total_hutang' => $debts->sum('jumlah_hutang')
                ],
                'message' => 'Data dashboard berhasil diambil'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    });

    // Employee debt route
    Route::get('dashboard/karyawan/hutang/{id}', function($id) {
        try {
            $debts = Debt::with('employee')
                ->where('employee_id', $id)
                ->orderBy('tgl_pinjam', 'desc')
                ->get();
                
            $totalHutang = $debts->sum('jumlah_hutang');
            $hutangBelumLunas = $debts->where('status', 1)
                                    ->where('keterangan', 'Belum Lunas')
                                    ->sum('jumlah_hutang');
            
            return response()->json([
                'data' => $debts->map(function($debt) {
                    return [
                        'id' => $debt->id,
                        'nama' => $debt->employee->nama,
                        'jumlah_hutang' => $debt->jumlah_hutang,
                        'tgl_pinjam' => $debt->tgl_pinjam,
                        'tgl_jatuh_tempo' => $debt->tgl_jatuh_tempo,
                        'alasan' => $debt->alasan,
                        'status' => $debt->status,
                        'keterangan' => $debt->keterangan,
                        'status_label' => $debt->status === 1 ? 'Diterima' : 
                                         ($debt->status === 2 ? 'Diproses' : 'Ditolak'),
                        'keterangan_label' => $debt->keterangan == "Lunas" ? 
                                            '<span class="badge text-bg-success">Lunas</span>' : 
                                            '<span class="badge text-bg-danger">Belum Lunas</span>'
                    ];
                }),
                'total_hutang' => $totalHutang,
                'hutang_belum_lunas' => $hutangBelumLunas,
                'message' => 'Data hutang berhasil diambil'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    });
});

// Admin-only routes (tanpa middleware employee.access)
Route::middleware('auth:sanctum','admin.access')->group(function () {
    Route::get('/hidden/all-employees', function() {
        $user = request()->user();
        
        // // Hanya admin (role_id 1) yang bisa akses
        // if ($user->role_id != 1) {
        //     return response()->json([
        //         'message' => 'Unauthorized. Admin access required.'
        //     ], 403);
        // }
        
        $employees = Employee::with('salary')->get();
        
        return response()->json([
            'data' => $employees,
            'message' => 'Semua data karyawan berhasil diambil'
        ]);
    });
});