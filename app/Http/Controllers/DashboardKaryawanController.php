<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Debt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DashboardKaryawanController extends Controller
{
    public function index($id)
    {
        return view('dashboard-karyawan.index', [
            'title' => 'Dashboard Karyawan',
            'apiUrl' => url("/api/dashboard/karyawan/{$id}"),
            'employeeId' => $id
        ]);
    }

    public function myProfile($id)
    {
        return view('dashboard-karyawan.profile', [
            'title' => 'Dashboard Karyawan | Profile',
            'apiProfileUrl' => url('/api/dashboard/karyawan/profile/' . $id)
        ]);
    }

    public function mySalary($id)
    {
        return view('dashboard-karyawan.salary', [
            'title' => 'Dashboard Karyawan | Gaji',
            'apiSalaryUrl' => url('/api/dashboard/karyawan/gaji/' . $id)
        ]);
    }

    public function editBiodata()
    {
        return view('dashboard-karyawan.edit-biodata', [
            'title' => 'Dashboard Karyawan | Edit Biodata',
            'employee' => Employee::find(auth()->user()->employee_id)
        ]);
    }

    public function updateBiodata(Request $request)
    {
        $data = [
            'salary_id' => auth()->user()->employee->salary_id,
            'nip' => auth()->user()->employee->nip,
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir' => $request->tgl_lahir,
            'no_telp' => $request->no_telp,
            'tgl_masuk' => auth()->user()->employee->tgl_masuk,
            'alamat' => $request->alamat,
            'no_rek' => $request->no_rek,
            'bank' => $request->bank,
            'status' => auth()->user()->employee->status
        ];

        Employee::where('id', auth()->user()->employee_id)->update($data);
        return redirect('/dashboard/karyawan/profile/'. auth()->user()->employee_id)->with('success', 'Biodata berhasil diubah');
    }

    public function editAccount()
    {
        return view('dashboard-karyawan.edit-account', [
            'title' => 'Dashboard Karyawan | Edit Profile Account',
            'employee' => Employee::find(auth()->user()->employee_id)
        ]);
    }

    public function updateAccount(Request $request)
    {
        $data = [
            'role_id' => auth()->user()->role_id,
            'employee_id' => auth()->user()->employee_id,
            'email' => $request->email,
            'password' => auth()->user()->password
        ];

        User::where('id', auth()->user()->id)->update($data);
        return redirect('/dashboard/karyawan/profile/'. auth()->user()->employee_id)->with('success', 'Informasi Akun berhasil diubah');
    }

    public function myDebt($id)
    {
        $employee = Employee::findOrFail($id);

        return view('dashboard-karyawan.debt', [
            'title' => 'Dashboard Karyawan | Hutang',
            'apiUrl' => url("/api/dashboard/karyawan/hutang/{$id}"),
            'employeeId' => $id,
            'selectedEmployee' => $employee
        ]);
    }

    public function pinjam(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'jumlah_hutang' => 'required|numeric|min:1',
            'alasan' => 'required|string|max:255'
        ]);

        Debt::create([
            'employee_id' => $request->employee_id,
            'jumlah_hutang' => $request->jumlah_hutang,
            'alasan' => $request->alasan,
            'status' => 2,
            'keterangan' => 'Belum Lunas'
        ]);

        return redirect()->back()->with('success', 'Pengajuan hutang berhasil dikirim!');
    }

    public function editPassword($id)
    {
        return view('dashboard-karyawan.change-password', [
            'title' => 'Ganti Password Karyawan',
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return back()->with("error", "Password lama tidak sesuai!");
        }

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with("success", "Password berhasil diubah!");
    }
}