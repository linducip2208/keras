<?php

namespace App\Filament\Widgets;

use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\PayrollRun;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class HrStatsOverview extends BaseWidget
{
    protected ?string $heading = 'Ringkasan HR Bulan Ini';

    protected function getStats(): array
    {
        $totalEmployees = Employee::whereIn('employment_status', ['active', 'probation'])->count();
        $newHires = Employee::whereMonth('join_date', now()->month)->whereYear('join_date', now()->year)->count();
        $resignations = Employee::whereMonth('last_working_date', now()->month)->whereYear('last_working_date', now()->year)->count();

        $todayAttendance = AttendanceLog::whereDate('date', today())->whereNotNull('check_in_at')->count();
        $attendanceRate = $totalEmployees > 0 ? round(($todayAttendance / $totalEmployees) * 100) : 0;

        $pendingLeaves = LeaveRequest::where('status', 'pending')->count();
        $lateToday = AttendanceLog::whereDate('date', today())->where('check_in_is_late', true)->count();
        $mockGpsAlerts = AttendanceLog::whereDate('date', '>=', now()->subDays(7))->where('check_in_is_mock_gps', true)->count();

        $latestPayroll = PayrollRun::latest()->first();
        $payrollGross = $latestPayroll ? (int) $latestPayroll->total_gross : 0;

        return [
            Stat::make('Karyawan Aktif', $totalEmployees)
                ->description($newHires . ' new hire bulan ini · ' . $resignations . ' resign')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Kehadiran Hari Ini', "{$attendanceRate}%")
                ->description("{$todayAttendance} dari {$totalEmployees} karyawan hadir")
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($attendanceRate >= 90 ? 'success' : ($attendanceRate >= 80 ? 'warning' : 'danger')),

            Stat::make('Telat Hari Ini', $lateToday)
                ->description($lateToday > 0 ? 'Perlu attention' : 'Semua tepat waktu')
                ->descriptionIcon('heroicon-m-clock')
                ->color($lateToday > 5 ? 'danger' : ($lateToday > 0 ? 'warning' : 'success')),

            Stat::make('🚨 Mock GPS Alert', $mockGpsAlerts)
                ->description($mockGpsAlerts > 0 ? '7 hari terakhir — investigate!' : 'Bersih')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color($mockGpsAlerts > 0 ? 'danger' : 'success'),

            Stat::make('Pending Approval', $pendingLeaves)
                ->description('Cuti menunggu persetujuan')
                ->descriptionIcon('heroicon-m-envelope-open')
                ->color($pendingLeaves > 0 ? 'warning' : 'gray'),

            Stat::make('Payroll Terakhir', 'Rp ' . number_format($payrollGross, 0, ',', '.'))
                ->description($latestPayroll ? "Periode " . ($latestPayroll->period->name ?? '-') . " — " . ucfirst($latestPayroll->status) : 'Belum ada payroll')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
        ];
    }
}
