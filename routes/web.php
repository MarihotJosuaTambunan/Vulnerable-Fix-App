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
Route::get('/dashboard/admin/{id}', [AdminController::class, 'index'])->middleware('admin');
Route::get('/dashboard/finance_manager/{id}', [PegawaiController::class, 'index'])->middleware('finance_manager');


/**
 * Route Role Karyawan
 * 
 */
Route::get('/dashboard/karyawan/{id}', [DashboardKaryawanController::class, 'index'])->middleware('karyawan');
Route::get('/dashboard/karyawan/profile/{id}', [DashboardKaryawanController::class, 'myProfile'])->middleware('karyawan');
Route::get('/dashboard/karyawan/profile/edit-biodata/{id}', [DashboardKaryawanController::class, 'editBiodata'])->middleware('karyawan');
Route::put('/dashboard/karyawan/profile/edit-biodata/{id}', [DashboardKaryawanController::class, 'updateBiodata'])->middleware('karyawan');
Route::get('/dashboard/karyawan/profile/edit-account/{id}', [DashboardKaryawanController::class, 'editAccount'])->middleware('karyawan');
Route::put('/dashboard/karyawan/profile/edit-account/{id}', [DashboardKaryawanController::class, 'updateAccount'])->middleware('karyawan');
Route::get('/dashboard/karyawan/gaji/{id}', [DashboardKaryawanController::class, 'mySalary'])->middleware('karyawan');
Route::get('/dashboard/karyawan/hutang/{id}', [DashboardKaryawanController::class, 'myDebt'])->middleware('karyawan');
Route::post('/dashboard/karyawan/hutang', [DashboardKaryawanController::class, 'pinjam'])->middleware('karyawan');
Route::get('/dashboard/karyawan/change-password/{id}', [DashboardKaryawanController::class, 'editPassword'])->middleware('karyawan');
Route::post('/dashboard/karyawan/change-password/', [DashboardKaryawanController::class, 'updatePassword'])->middleware('karyawan');



/**
 * Route Profile & Ganti Password (Admin & finance_manager)
 * 
 */
Route::get('/profile', [AdminController::class, 'profile'])->middleware('admin_finance');
Route::get('/profile/edit-account', [AdminController::class, 'editAccount'])->middleware('admin_finance');
Route::get('/profile/edit-biodata', [AdminController::class, 'editBiodata'])->middleware('admin_finance');
Route::post('/profile/edit-account', [AdminController::class, 'updateAccount'])->middleware('admin_finance');
Route::post('/profile/edit-biodata', [AdminController::class, 'updateBiodata'])->middleware('admin_finance');

Route::get('/profile/ganti-password', [AdminController::class, 'editPassword'])->middleware('admin_finance');
Route::post('/profile/ganti-password', [AdminController::class, 'updatePassword'])->middleware('admin_finance');


/*
|--------------------------------------------------------------------------
| Route Resource
|--------------------------------------------------------------------------
|
/
*/

Route::resource('/users', UserController::class)->middleware('admin');
Route::resource('/kategori', KategoriController::class)->middleware('admin_finance');
Route::resource('/data/pemasukan', IncomeController::class)->middleware('admin_finance');
Route::resource('/data/pengeluaran', OutcomeController::class)->middleware('admin_finance');
Route::resource('/hutang', DebtController::class)->middleware('admin_finance');
Route::resource('/gaji', SalaryController::class)->middleware('admin_finance');
Route::resource('/karyawan', EmployeeController::class)->middleware('admin');
Route::resource('/gaji-karyawan', EmployeeSalaryController::class)->middleware('admin_finance');


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
Route::get('/cetak-laporan', [CetakController::class, 'index'])->middleware('admin_finance');
Route::get('/cetak-laporan/print-income', [CetakController::class, 'printIncome'])->middleware('admin_finance');
Route::get('/cetak-laporan/print-outcome', [CetakController::class, 'printOutcome'])->middleware('admin_finance');
Route::get('/cetak-laporan/pdf-income', [CetakController::class, 'createPDFIncome'])->middleware('admin_finance');
Route::get('/cetak-laporan/pdf-outcome', [CetakController::class, 'createPDFOutcome'])->middleware('admin_finance');
Route::get('/cetak-laporan/excel-income', [CetakController::class, 'excelIncome'])->middleware('admin_finance');
Route::get('/cetak-laporan/excel-outcome', [CetakController::class, 'excelOutcome'])->middleware('admin_finance');

Route::get('/cetak-laporan/print-semua-pemasukan', [CetakController::class, 'printAllIncome'])->middleware('admin_finance');
Route::get('/cetak-laporan/pdf-semua-pemasukan', [CetakController::class, 'allPDFIncome'])->middleware('admin_finance');
Route::get('/cetak-laporan/excel-semua-pemasukan', [CetakController::class, 'allExcelIncome'])->middleware('admin_finance');

Route::get('/cetak-laporan/print-semua-pengeluaran', [CetakController::class, 'printAllOutcome'])->middleware('admin_finance');
Route::get('/cetak-laporan/pdf-semua-pengeluaran', [CetakController::class, 'allPDFOutcome'])->middleware('admin_finance');
Route::get('/cetak-laporan/excel-semua-pengeluaran', [CetakController::class, 'allExcelOutcome'])->middleware('admin_finance');

// Berdasarkan Bulan & Tahun
Route::get('/cetak-print-laporan-income-bulan', [CetakController::class, 'getDataIncomeByMonth'])->middleware('admin_finance');


// Hutang
Route::get('/cetak-laporan/print-hutang', [CetakController::class, 'printDebt'])->middleware('admin_finance');
Route::get('/cetak-laporan/pdf-hutang', [CetakController::class, 'PDFDebt'])->middleware('admin_finance');
Route::get('/cetak-laporan/excel-hutang', [CetakController::class, 'excelDebt'])->middleware('admin_finance');

Route::get('/cetak-laporan/print-semua-hutang', [CetakController::class, 'printAllDebt'])->middleware('admin_finance');
Route::get('/cetak-laporan/pdf-semua-hutang', [CetakController::class, 'allPDFDebt'])->middleware('admin_finance');
Route::get('/cetak-laporan/excel-semua-hutang', [CetakController::class, 'allExcelDebt'])->middleware('admin_finance');

// Gaji Karyawan
Route::get('/cetak-print-gaji-karyawan', [CetakController::class, 'printGajiKaryawan'])->middleware('admin_finance');
Route::get('/cetak-pdf-gaji-karyawan', [CetakController::class, 'gajiKaryawanPDF'])->middleware('admin_finance');
// Route::get('/cetak-print-semua-gaji-karyawan', [CetakController::class, 'printAllGajiKaryawan'])->middleware('admin_finance');

Route::get('cetak-laporan/print-gaji-karyawan', [CetakController::class, 'printSemuaGajiKaryawan'])->middleware('admin_finance');
Route::get('cetak-laporan/pdf-gaji-karyawan', [CetakController::class, 'PDFSemuaGajiKaryawan'])->middleware('admin_finance');
Route::get('cetak-laporan/excel-gaji-karyawan', [CetakController::class, 'excelSemuaGajiKaryawan'])->middleware('admin_finance');
