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
    /**
     * Method Controller untuk Menu Dashboard Karyawan
     * 
     */
    public function index($id)
    {

    // Hanya user dengan role_id 3 (finance_manager) yang boleh akses
    // if (auth()->user()->employee_id != $id) {
    //     abort(403, 'Akses Ditolak');
    // }
        return view('dashboard-karyawan.index', [
        'title' => 'Dashboard Karyawan',
        'apiUrl' => url("/api/dashboard/karyawan/{$id}"),
        'employeeId' => $id
    ]);
    }   



        /**
     * Method Controller untuk Menu Profile Karyawan
     */
    public function myProfile($id)
{
    // if (auth()->user()->employee_id != $id) {
    //     abort(403, 'Akses Ditolak');
    // }
    
    // Hanya mengirim URL API ke view
    return view('dashboard-karyawan.profile', [
        'title'         => 'Dashboard Karyawan | Profile',
        'apiProfileUrl' => url('/api/dashboard/karyawan/profile/' . $id)
    ]);
}

    /**
     * Method Controller untuk Menu Gaji Saya
     */
    public function mySalary($id)
    {
        // if (auth()->user()->employee_id != $id) {
        //     abort(403, 'Akses Ditolak');
        // }

        // Hanya mengirim URL API ke view
        return view('dashboard-karyawan.salary', [
            'title'        => 'Dashboard Karyawan | Gaji',
            'apiSalaryUrl' => url('/api/dashboard/karyawan/gaji/' . $id)
        ]);
    }


    public function editBiodata()
    {
        return view('dashboard-karyawan.edit-biodata', [
            'title'     => 'Dashboard Karyawan | Edit Biodata',
            'employee'  => Employee::find(auth()->user()->employee_id)
        ]);
    }

    public function updateBiodata(Request $request)
    {
        $data = [
            // 'user_id'       => $request->user_id,
            'salary_id'     => auth()->user()->employee->salary_id,
            'nip'           => auth()->user()->employee->nip,
            'nama'          => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir'  => $request->tempat_lahir,
            'tgl_lahir'     => $request->tgl_lahir,
            'no_telp'       => $request->no_telp,
            'tgl_masuk'     => auth()->user()->employee->tgl_masuk,
            'alamat'        => $request->alamat,
            'no_rek'        => $request->no_rek,
            'bank'          => $request->bank,
            'status'        => auth()->user()->employee->status
        ];

        Employee::where('id', auth()->user()->employee_id)->update($data);
        return redirect('/dashboard/karyawan/profile/'. auth()->user()->employee_id)->with('success', 'Biodata berhasil diubah');
    }

    public function editAccount()
    {
        return view('dashboard-karyawan.edit-account', [
            'title'     => 'Dashboard Karyawan | Edit Profile Account',
            'employee'  => Employee::find(auth()->user()->employee_id)
        ]);
    }

    public function updateAccount(Request $request)
    {
        $data = [
            // auth()->user() digunakan untuk mengambil data saat ini yang sedang login
            // $request merupakan data terbaru yang diambil dari form input
            'role_id'       => auth()->user()->role_id,
            'employee_id'   => auth()->user()->employee_id,
            'email'         => $request->email,
            'password'      => auth()->user()->password
        ];

        // update tabel user dimana id = id user yang sedang login
        User::where('id', auth()->user()->id)->update($data);
        return redirect('/dashboard/karyawan/profile/'. auth()->user()->employee_id)->with('success', 'Informasi Akun berhasil diubah');
    }




    /**
     * Method Controller untuk menu Pinjam Hutang
     * 
     */
 public function myDebt($id)
{
    // if (auth()->user()->employee_id != $id) {
    //     abort(403, 'Akses Ditolak');
    // }
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
        'status' => 2, // Status diproses
        'keterangan' => 'Belum Lunas'
    ]);

    return redirect()->back()->with('success', 'Pengajuan hutang berhasil dikirim!');
}






    public function editPassword($id)
    {
        if (auth()->user()->employee_id != $id) {
            abort(403, 'Akses Ditolak');
        }
  
        return view('dashboard-karyawan.change-password', [
            'title' => 'Ganti Password Karyawan',
            // 'users' => Employee::find(auth()->user()->employee_id)
        ]);
    }

    public function updatePassword(Request $request)
    {
        # Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);


        #Match The Old Password
        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return back()->with("error", "Old Password Doesn't match!");
        }


        #Update the new Password
        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with("success", "Password changed successfully!");
    }
}
