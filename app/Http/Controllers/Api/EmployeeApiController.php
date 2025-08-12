<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeSalary;

class EmployeeApiController extends Controller
{
    public function getMyProfile(Request $request)
    {
        $employee = Employee::with('salary')->find($request->user()->employee_id);
        if (!$employee) return response()->json(['message' => 'Not found'], 404);

        return response()->json(['success' => true, 'employee' => $employee]);
    }

    public function getMySalary(Request $request)
    {
        $salaries = EmployeeSalary::with(['employee', 'salary'])
                        ->where('karyawan_id', $request->user()->employee_id)
                        ->get();

        return response()->json(['success' => true, 'data' => $salaries]);
    }
}
