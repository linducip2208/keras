<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DemoAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = DB::table('tenants')->where('slug', 'acme')->value('id');
        if (!$tenantId) return;

        $employees = DB::table('employees')->where('tenant_id', $tenantId)->where('employment_status', 'active')->get();
        $shiftId = DB::table('shifts')->where('tenant_id', $tenantId)->where('code', 'REG')->value('id');
        $now = now();

        foreach ($employees as $emp) {
            for ($d = 1; $d <= 30; $d++) {
                $date = $now->copy()->subDays($d);
                if ($date->isWeekend()) continue;

                // 95% attendance rate
                if (rand(1, 100) > 95) continue;

                $isLate = rand(1, 100) <= 15; // 15% late
                $lateMinutes = $isLate ? rand(5, 45) : 0;

                $checkIn = $date->copy()->setTime(9, 0)->addMinutes($lateMinutes);
                $checkOut = $checkIn->copy()->addHours(rand(8, 9))->addMinutes(rand(0, 30));
                $workedMinutes = $checkOut->diffInMinutes($checkIn);

                DB::table('attendance_logs')->insertOrIgnore([
                    'tenant_id' => $tenantId,
                    'employee_id' => $emp->id,
                    'date' => $date->toDateString(),
                    'shift_id' => $shiftId,
                    'check_in_at' => $checkIn,
                    'check_in_latitude' => -6.2088 + (rand(-100, 100) / 10000),
                    'check_in_longitude' => 106.8456 + (rand(-100, 100) / 10000),
                    'check_in_method' => collect(['gps', 'gps', 'gps', 'fingerprint', 'face'])->random(),
                    'check_in_is_late' => $isLate,
                    'check_in_late_minutes' => $lateMinutes,
                    'check_in_is_mock_gps' => rand(1, 200) === 1, // 0.5% mock gps suspect
                    'check_out_at' => $checkOut,
                    'check_out_latitude' => -6.2088 + (rand(-100, 100) / 10000),
                    'check_out_longitude' => 106.8456 + (rand(-100, 100) / 10000),
                    'check_out_method' => 'gps',
                    'worked_minutes' => $workedMinutes,
                    'overtime_minutes' => max(0, $workedMinutes - 480),
                    'status' => 'present',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Sample leave requests (5 pending, 3 approved)
        $leaveTypeId = DB::table('leave_types')->where('tenant_id', $tenantId)->where('code', 'CT')->value('id');
        $sampleEmps = $employees->take(8);
        $i = 0;
        foreach ($sampleEmps as $emp) {
            $isApproved = $i++ < 3;
            $start = $now->copy()->addDays(rand(3, 30));
            $end = $start->copy()->addDays(rand(0, 4));
            DB::table('leave_requests')->insert([
                'tenant_id' => $tenantId,
                'employee_id' => $emp->id,
                'leave_type_id' => $leaveTypeId,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'days_count' => $end->diffInDays($start) + 1,
                'reason' => collect(['Liburan keluarga', 'Acara keluarga', 'Refresh', 'Cuti panjang', 'Mudik'])->random(),
                'status' => $isApproved ? 'approved' : 'pending',
                'applied_at' => $now->copy()->subDays(rand(1, 5)),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->command->info('Demo: ~25 employees × ~22 attendance days + 8 leave requests seeded.');
    }
}
