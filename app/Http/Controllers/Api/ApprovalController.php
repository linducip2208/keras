<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApprovalRequestStep;
use App\Models\Employee;
use App\Services\Approval\ApprovalEngine;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function __construct(private ApprovalEngine $engine) {}

    public function inbox(Request $request)
    {
        $employee = $this->resolveEmployee($request);
        $steps = ApprovalRequestStep::with('approvalRequest.requestable')
            ->where('approver_employee_id', $employee->id)
            ->where('action', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
        return response()->json(['data' => $steps]);
    }

    public function approve(Request $request, int $id)
    {
        $employee = $this->resolveEmployee($request);
        $step = ApprovalRequestStep::where('approver_employee_id', $employee->id)
            ->where('action', 'pending')
            ->findOrFail($id);
        $this->engine->approve($step, $request->input('comment'));
        return response()->json(['message' => 'Approved']);
    }

    public function reject(Request $request, int $id)
    {
        $employee = $this->resolveEmployee($request);
        $step = ApprovalRequestStep::where('approver_employee_id', $employee->id)
            ->where('action', 'pending')
            ->findOrFail($id);
        $this->engine->reject($step, $request->input('comment', 'No reason'));
        return response()->json(['message' => 'Rejected']);
    }

    private function resolveEmployee(Request $request): Employee
    {
        $employee = Employee::where('user_id', $request->user()->id)->first();
        abort_if(!$employee, 404, 'Employee profile not found');
        return $employee;
    }
}
