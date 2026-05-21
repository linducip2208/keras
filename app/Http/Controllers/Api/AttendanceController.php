<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo_path' => 'nullable|string',
            'method' => 'required|in:gps,fingerprint,qr,face,manual',
            'is_mock_gps' => 'sometimes|boolean',
        ]);

        $employee = $this->resolveEmployee($request);
        $today = today();

        $log = AttendanceLog::firstOrNew(['employee_id' => $employee->id, 'date' => $today]);
        if ($log->check_in_at) {
            return response()->json(['error' => 'Sudah check-in hari ini pada ' . $log->check_in_at->format('H:i')], 422);
        }

        $branch = $employee->branch_id ? Branch::find($employee->branch_id) : null;
        $shift = Shift::where('tenant_id', $employee->tenant_id)->where('code', 'REG')->first();

        $log->fill([
            'tenant_id' => $employee->tenant_id,
            'shift_id' => $shift?->id,
            'check_in_at' => now(),
            'check_in_latitude' => $request->latitude,
            'check_in_longitude' => $request->longitude,
            'check_in_address' => $request->address,
            'check_in_photo_path' => $request->photo_path,
            'check_in_method' => $request->method,
            'check_in_branch_id' => $branch?->id,
            'check_in_is_mock_gps' => $request->boolean('is_mock_gps'),
            'status' => 'present',
        ]);

        if ($shift && $shift->start_time) {
            $shiftStart = Carbon::parse($today->toDateString() . ' ' . $shift->start_time);
            $lateMinutes = max(0, now()->diffInMinutes($shiftStart, false) * -1);
            if ($lateMinutes > $shift->allowed_late_minutes) {
                $log->check_in_is_late = true;
                $log->check_in_late_minutes = (int) $lateMinutes;
            }
        }

        $log->save();

        return response()->json([
            'message' => 'Check-in berhasil',
            'data' => $log,
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo_path' => 'nullable|string',
            'method' => 'required|in:gps,fingerprint,qr,face,manual',
        ]);

        $employee = $this->resolveEmployee($request);
        $today = today();
        $log = AttendanceLog::where('employee_id', $employee->id)->where('date', $today)->first();

        if (!$log) {
            return response()->json(['error' => 'Belum check-in hari ini'], 422);
        }
        if ($log->check_out_at) {
            return response()->json(['error' => 'Sudah check-out hari ini pada ' . $log->check_out_at->format('H:i')], 422);
        }

        $log->update([
            'check_out_at' => now(),
            'check_out_latitude' => $request->latitude,
            'check_out_longitude' => $request->longitude,
            'check_out_address' => $request->address,
            'check_out_photo_path' => $request->photo_path,
            'check_out_method' => $request->method,
            'check_out_is_mock_gps' => $request->boolean('is_mock_gps'),
            'worked_minutes' => now()->diffInMinutes($log->check_in_at),
        ]);

        return response()->json(['message' => 'Check-out berhasil', 'data' => $log]);
    }

    public function today(Request $request)
    {
        $employee = $this->resolveEmployee($request);
        $log = AttendanceLog::where('employee_id', $employee->id)->where('date', today())->first();
        return response()->json(['data' => $log]);
    }

    public function logs(Request $request)
    {
        $employee = $this->resolveEmployee($request);
        $logs = AttendanceLog::where('employee_id', $employee->id)
            ->orderByDesc('date')
            ->limit(30)
            ->get();
        return response()->json(['data' => $logs]);
    }

    private function resolveEmployee(Request $request): Employee
    {
        $employee = Employee::where('user_id', $request->user()->id)->first();
        abort_if(!$employee, 404, 'Employee profile not found');
        return $employee;
    }
}
