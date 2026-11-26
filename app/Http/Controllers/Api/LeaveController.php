<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\Approval\ApprovalEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    public function __construct(private ApprovalEngine $approval) {}

    public function types(Request $request)
    {
        $employee = $this->resolveEmployee($request);
        $types = LeaveType::where('tenant_id', $employee->tenant_id)->where('is_active', true)->get();
        return response()->json(['data' => $types]);
    }

    public function balances(Request $request)
    {
        $employee = $this->resolveEmployee($request);
        $balances = LeaveBalance::where('employee_id', $employee->id)
            ->where('year', now()->year)
            ->with('leaveType')
            ->get();
        return response()->json(['data' => $balances]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
            'is_half_day' => 'sometimes|boolean',
        ]);

        $employee = $this->resolveEmployee($request);
        // Tenant-scoped: menutup IDOR tebak ID lintas tenant.
        $type = LeaveType::where('tenant_id', $employee->tenant_id)->findOrFail($request->leave_type_id);

        $days = Carbon::parse($request->end_date)->diffInDays(Carbon::parse($request->start_date)) + 1;
        if ($request->boolean('is_half_day')) $days = 0.5;

        return DB::transaction(function () use ($request, $employee, $type, $days) {
            if ($type->requires_balance) {
                // Kunci baris saldo agar double-submit concurrent tidak lolos.
                $balance = LeaveBalance::where('employee_id', $employee->id)
                    ->where('leave_type_id', $type->id)
                    ->where('year', now()->year)
                    ->lockForUpdate()
                    ->first();

                if (!$balance) {
                    $balance = LeaveBalance::create(
                        ['tenant_id' => $employee->tenant_id, 'employee_id' => $employee->id, 'leave_type_id' => $type->id, 'year' => now()->year],
                        ['allocated' => $type->days_per_year ?? 0]
                    );
                }
                $available = $balance->allocated - $balance->used - $balance->pending;
                if ($available < $days) {
                    abort(422, "Saldo cuti tidak cukup. Tersisa: {$available} hari");
                }
                $balance->increment('pending', $days);
            }

            $leave = LeaveRequest::create([
                'tenant_id' => $employee->tenant_id,
                'employee_id' => $employee->id,
                'leave_type_id' => $type->id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'days_count' => $days,
                'is_half_day' => $request->boolean('is_half_day'),
                'reason' => $request->reason,
                'status' => 'pending',
                'applied_at' => now(),
            ]);

            $this->approval->submit(
                applicableType: 'leave',
                requestable: $leave,
                requester: $employee,
            );

            return response()->json(['message' => 'Request cuti terkirim', 'data' => $leave->fresh()]);
        });
    }

    public function index(Request $request)
    {
        $employee = $this->resolveEmployee($request);
        $leaves = LeaveRequest::where('employee_id', $employee->id)
            ->orderByDesc('applied_at')
            ->limit(50)
            ->with('leaveType')
            ->get();
        return response()->json(['data' => $leaves]);
    }

    private function resolveEmployee(Request $request): Employee
    {
        $employee = Employee::where('user_id', $request->user()->id)->first();
        abort_if(!$employee, 404, 'Employee profile not found');
        return $employee;
    }
}
