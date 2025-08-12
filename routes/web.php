<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\OutcomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CetakController;
use App\Http\Controllers\DashboardKaryawanController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\EmployeeSalaryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route Authentication
Route::get('/', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/', [LoginController::class, 'dologin'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');

// Route::get('/register', [RegisterController::class, 'index'])->middleware('guest');
// Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');

// Route Halaman Admin & finance_manager
Route::get('/dashboard/admin/{id}', [AdminController::class, 'index']);
Route::get('/dashboard/finance_manager/{id}', [PegawaiController::class, 'index']);


/**
 * Route Role Karyawan
 * 
 */
Route::get('/dashboard/karyawan/{id}', [DashboardKaryawanController::class, 'index']);
Route::get('/dashboard/karyawan/profile/{id}', [DashboardKaryawanController::class, 'myProfile']);
Route::get('/dashboard/karyawan/profile/edit-biodata/{id}', [DashboardKaryawanController::class, 'editBiodata']);
Route::put('/dashboard/karyawan/profile/edit-biodata/{id}', [DashboardKaryawanController::class, 'updateBiodata']);
Route::get('/dashboard/karyawan/profile/edit-account/{id}', [DashboardKaryawanController::class, 'editAccount']);
Route::put('/dashboard/karyawan/profile/edit-account/{id}', [DashboardKaryawanController::class, 'updateAccount']);
Route::get('/dashboard/karyawan/gaji/{id}', [DashboardKaryawanController::class, 'mySalary']);
Route::get('/dashboard/karyawan/hutang/{id}', [DashboardKaryawanController::class, 'myDebt']);
Route::post('/dashboard/karyawan/hutang', [DashboardKaryawanController::class, 'pinjam']);
Route::get('/dashboard/karyawan/change-password/{id}', [DashboardKaryawanController::class, 'editPassword']);
Route::post('/dashboard/karyawan/change-password/', [DashboardKaryawanController::class, 'updatePassword']);



/**
 * Route Profile & Ganti Password (Admin & finance_manager)
 * 
 */
Route::get('/profile', [AdminController::class, 'profile']);
Route::get('/profile/edit-account', [AdminController::class, 'editAccount']);
Route::get('/profile/edit-biodata', [AdminController::class, 'editBiodata']);
Route::post('/profile/edit-account', [AdminController::class, 'updateAccount']);
Route::post('/profile/edit-biodata', [AdminController::class, 'updateBiodata']);

Route::get('/profile/ganti-password', [AdminController::class, 'editPassword']);
Route::post('/profile/ganti-password', [AdminController::class, 'updatePassword']);


/*
|--------------------------------------------------------------------------
| Route Resource
|--------------------------------------------------------------------------
|
/
*/

Route::resource('/users', UserController::class);
Route::resource('/kategori', KategoriController::class);
Route::resource('/data/pemasukan', IncomeController::class);
Route::resource('/data/pengeluaran', OutcomeController::class);
Route::resource('/hutang', DebtController::class);
Route::resource('/gaji', SalaryController::class);
Route::resource('/karyawan', EmployeeController::class);
Route::resource('/gaji-karyawan', EmployeeSalaryController::class);


/*
|--------------------------------------------------------------------------
| Route Cetak Laporan: PRINT - PDF - EXCEL
|--------------------------------------------------------------------------
|
| Disini kamu bisa mengatur penjaluran (routing) halaman utama cetak laporan, 
| halaman untuk cetak PRINT, PDF & EXCEL.
|
*/

// Pemasukan - Pengeluaran / Income - Outcome
Route::get('/cetak-laporan', [CetakController::class, 'index']);
Route::get('/cetak-laporan/print-income', [CetakController::class, 'printIncome']);
Route::get('/cetak-laporan/print-outcome', [CetakController::class, 'printOutcome']);
Route::get('/cetak-laporan/pdf-income', [CetakController::class, 'createPDFIncome']);
Route::get('/cetak-laporan/pdf-outcome', [CetakController::class, 'createPDFOutcome']);
Route::get('/cetak-laporan/excel-income', [CetakController::class, 'excelIncome']);
Route::get('/cetak-laporan/excel-outcome', [CetakController::class, 'excelOutcome']);

Route::get('/cetak-laporan/print-semua-pemasukan', [CetakController::class, 'printAllIncome']);
Route::get('/cetak-laporan/pdf-semua-pemasukan', [CetakController::class, 'allPDFIncome']);
Route::get('/cetak-laporan/excel-semua-pemasukan', [CetakController::class, 'allExcelIncome']);

Route::get('/cetak-laporan/print-semua-pengeluaran', [CetakController::class, 'printAllOutcome']);
Route::get('/cetak-laporan/pdf-semua-pengeluaran', [CetakController::class, 'allPDFOutcome']);
Route::get('/cetak-laporan/excel-semua-pengeluaran', [CetakController::class, 'allExcelOutcome']);

// Berdasarkan Bulan & Tahun
Route::get('/cetak-print-laporan-income-bulan', [CetakController::class, 'getDataIncomeByMonth']);


// Hutang
Route::get('/cetak-laporan/print-hutang', [CetakController::class, 'printDebt']);
Route::get('/cetak-laporan/pdf-hutang', [CetakController::class, 'PDFDebt']);
Route::get('/cetak-laporan/excel-hutang', [CetakController::class, 'excelDebt']);

Route::get('/cetak-laporan/print-semua-hutang', [CetakController::class, 'printAllDebt']);
Route::get('/cetak-laporan/pdf-semua-hutang', [CetakController::class, 'allPDFDebt']);
Route::get('/cetak-laporan/excel-semua-hutang', [CetakController::class, 'allExcelDebt']);

// Gaji Karyawan
Route::get('/cetak-print-gaji-karyawan', [CetakController::class, 'printGajiKaryawan']);
Route::get('/cetak-pdf-gaji-karyawan', [CetakController::class, 'gajiKaryawanPDF']);
// Route::get('/cetak-print-semua-gaji-karyawan', [CetakController::class, 'printAllGajiKaryawan']);

Route::get('cetak-laporan/print-gaji-karyawan', [CetakController::class, 'printSemuaGajiKaryawan']);
Route::get('cetak-laporan/pdf-gaji-karyawan', [CetakController::class, 'PDFSemuaGajiKaryawan']);
Route::get('cetak-laporan/excel-gaji-karyawan', [CetakController::class, 'excelSemuaGajiKaryawan']);
