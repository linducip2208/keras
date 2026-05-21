<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payslip;
use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function index(Request $request)
    {
        $employee = $this->resolveEmployee($request);
        $payslips = Payslip::where('employee_id', $employee->id)
            ->orderByDesc('slip_date')
            ->limit(12)
            ->get();
        return response()->json(['data' => $payslips]);
    }

    public function show(Request $request, int $id)
    {
        $employee = $this->resolveEmployee($request);
        $payslip = Payslip::with(['payrollItem.components.component'])
            ->where('employee_id', $employee->id)
            ->findOrFail($id);
        $payslip->update(['opened_at' => now()]);
        return response()->json(['data' => $payslip]);
    }

    private function resolveEmployee(Request $request): Employee
    {
        $employee = Employee::where('user_id', $request->user()->id)->first();
        abort_if(!$employee, 404, 'Employee profile not found');
        return $employee;
    }
}
