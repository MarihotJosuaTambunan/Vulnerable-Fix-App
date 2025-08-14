<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeApiController;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Debt;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Route::get('/unprotected-employees', function () {
//     $employees = Employee::all();
//     return response()->json([
//         'status' => 'success',
//         'message' => 'Data karyawan tanpa proteksi.',
//         'data' => $employees
//     ]);
// });



// API untuk menampilkan data profile karyawan TANPA data salary
Route::get('/dashboard/karyawan/profile/{id}', function($id) {
    try {
        // Ambil data karyawan TANPA relasi salary
        $employee = Employee::find($id);
        
        if(!$employee) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan'], 404);
        }
        
        // Format data yang akan dikembalikan
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
            // Tambahkan field lain yang diperlukan
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

// API untuk menampilkan data gaji karyawan
Route::get('dashboard/karyawan/gaji/{id}', function($id) {
    try {
        // Ambil data gaji dengan relasi ke salary
        $salaries = EmployeeSalary::with(['salary'])
            ->where('karyawan_id', $id)
            ->get(['id', 'karyawan_id', 'gaji_id', 'tgl_gajian', 'created_at', 'updated_at']);
            
        if ($salaries->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Belum ada data gaji',
                    'total_keseluruhan' => 0
                ]);
            }
        
        // Format data yang akan dikembalikan
        $formattedSalaries = $salaries->map(function($item) {
            return [
                'id' => $item->id,
                'tgl_gajian' => $item->tgl_gajian,
                'jabatan' => $item->salary->jabatan ?? 'Tidak diketahui', // Tambah kolom jabatan
                'gaji_pokok' => $item->salary->gaji_pokok ?? 0,
                'tunjangan_transport' => $item->salary->tj_transport ?? 0,
                'uang_makan' => $item->salary->uang_makan ?? 0,
                'total_gaji' => ($item->salary->gaji_pokok ?? 0) + 
                                ($item->salary->tj_transport ?? 0) + 
                                ($item->salary->uang_makan ?? 0)
            ];
        });
        
        // Hitung total keseluruhan
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

// API tersembunyi untuk menampilkan semua data karyawan (tidak digunakan di frontend)
Route::get('/hidden/all-employees', function() {
    $employees = Employee::with('salary')->get();
    
    return response()->json([
        'data' => $employees,
        'message' => 'Semua data karyawan berhasil diambil'
    ]);
});


// API Dashboard Karyawan
Route::get('/dashboard/karyawan/{id}', function($id) {
    try {
        $employee = Employee::with('salary')->find($id);
        
        if(!$employee) {
            return response()->json(['message' => 'Data karyawan tidak ditemukan'], 404);
        }
        
        // Hitung total gaji
        $salaries = EmployeeSalary::where('karyawan_id', $id)
            ->with('salary')
            ->get();
            
        $totalGaji = $salaries->sum(function($item) {
            return $item->salary->gaji_pokok + $item->salary->tj_transport + $item->salary->uang_makan;
        });
        
        // Hitung hutang belum lunas
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


// API Hutang Karyawan (GET Only)
Route::get('dashboard/karyawan/hutang/{id}', function($id) {
    try {
        // Verifikasi hanya bisa akses data sendiri
        // if (auth()->user()->employee_id != $id) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

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