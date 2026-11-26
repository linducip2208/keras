<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

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

        // Mock-GPS ditolak di server (bukan sekadar dicatat).
        if ($request->boolean('is_mock_gps') && config('attendance.reject_mock_gps', true)) {
            return response()->json(['error' => 'Lokasi terdeteksi palsu (mock GPS). Matikan aplikasi fake-GPS dan coba lagi.'], 422);
        }

        $employee = $this->resolveEmployee($request);
        $today = today();

        $log = AttendanceLog::firstOrNew(['employee_id' => $employee->id, 'date' => $today]);
        if ($log->check_in_at) {
            return response()->json(['error' => 'Sudah check-in hari ini pada ' . $log->check_in_at->format('H:i')], 422);
        }

        // Validasi selfie: harus file hasil upload resmi, bukan path lokal HP.
        if ($request->photo_path) {
            $this->assertServerPhoto($request->photo_path);
        }

        $branch = $employee->branch_id ? Branch::find($employee->branch_id) : null;
        // Shift mengikuti jadwal karyawan hari ini; fallback ke REG bila tak ada jadwal.
        $schedule = EmployeeSchedule::with('shift')
            ->where('employee_id', $employee->id)
            ->where('date', $today->toDateString())
            ->first();
        $shift = ($schedule && !$schedule->is_off_day) ? $schedule->shift
            : Shift::where('tenant_id', $employee->tenant_id)->where('code', 'REG')->first();

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
            'is_mock_gps' => 'sometimes|boolean',
        ]);

        if ($request->boolean('is_mock_gps') && config('attendance.reject_mock_gps', true)) {
            return response()->json(['error' => 'Lokasi terdeteksi palsu (mock GPS). Matikan aplikasi fake-GPS dan coba lagi.'], 422);
        }

        $employee = $this->resolveEmployee($request);
        $today = today();
        $log = AttendanceLog::where('employee_id', $employee->id)->where('date', $today)->first();

        if (!$log) {
            return response()->json(['error' => 'Belum check-in hari ini'], 422);
        }
        if ($log->check_out_at) {
            return response()->json(['error' => 'Sudah check-out hari ini pada ' . $log->check_out_at->format('H:i')], 422);
        }

        if ($request->photo_path) {
            $this->assertServerPhoto($request->photo_path);
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

    /**
     * Upload selfie clock-in (multipart). Mengembalikan server path yang
     * wajib dipakai sebagai photo_path pada check-in/out.
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:' . config('attendance.photo_max_kb', 4096),
        ]);

        $employee = $this->resolveEmployee($request);
        $disk = config('attendance.photo_disk', 'local');
        $path = $request->file('photo')->store(
            config('attendance.photo_dir') . "/{$employee->tenant_id}/{$employee->id}/" . today()->toDateString(),
            $disk
        );

        return response()->json(['message' => 'Foto tersimpan', 'photo_path' => $path]);
    }

    /**
     * Pastikan photo_path adalah file hasil uploadPhoto di storage server,
     * bukan path lokal dari HP (/data/..., /storage/emulated/...).
     */
    private function assertServerPhoto(string $path): void
    {
        $dir = config('attendance.photo_dir', 'attendance-photos');
        $disk = config('attendance.photo_disk', 'local');

        if (!str_starts_with($path, $dir . '/') || str_contains($path, '..')) {
            abort(422, 'photo_path tidak valid. Upload selfie via endpoint /attendance/photo.');
        }
        if (!Storage::disk($disk)->exists($path)) {
            abort(422, 'File selfie tidak ditemukan di server. Upload ulang selfie.');
        }
    }

    private function resolveEmployee(Request $request): Employee
    {
        $employee = Employee::where('user_id', $request->user()->id)->first();
        abort_if(!$employee, 404, 'Employee profile not found');
        return $employee;
    }
}
