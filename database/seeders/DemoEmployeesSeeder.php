<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoEmployeesSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = DB::table('tenants')->where('slug', 'acme')->value('id');
        if (!$tenantId) return;

        $companyId = DB::table('companies')->where('tenant_id', $tenantId)->value('id');
        $branches = DB::table('branches')->where('tenant_id', $tenantId)->pluck('id', 'code');
        $depts = DB::table('departments')->where('tenant_id', $tenantId)->pluck('id', 'code');
        $jobTitles = DB::table('job_titles')->where('tenant_id', $tenantId)->pluck('id', 'name');
        $jobLevels = DB::table('job_levels')->where('tenant_id', $tenantId)->pluck('id', 'code');

        $employees = [
            ['Andi Wijaya', 'M', 'TK_0', 'ENG', 'Engineering Manager', 'L5', 'HQ', 15_000_000, 'andi@acme.test'],
            ['Budi Santoso', 'M', 'K_1', 'ENG', 'Senior Software Engineer', 'L3', 'HQ', 12_500_000, 'budi@acme.test'],
            ['Citra Lestari', 'F', 'TK_0', 'ENG', 'Software Engineer', 'L2', 'HQ', 8_500_000, 'citra@acme.test'],
            ['Dani Pratama', 'M', 'K_2', 'ENG', 'Software Engineer', 'L2', 'HQ', 8_000_000, 'dani@acme.test'],
            ['Erna Wati', 'F', 'TK_0', 'ENG', 'Software Engineer', 'L2', 'BDG', 7_500_000, 'erna@acme.test'],
            ['Fajar Nugroho', 'M', 'K_0', 'SLS', 'Sales Manager', 'L5', 'HQ', 14_000_000, 'fajar@acme.test'],
            ['Gita Permana', 'F', 'TK_1', 'SLS', 'Sales Executive', 'L2', 'HQ', 7_000_000, 'gita@acme.test'],
            ['Hadi Susilo', 'M', 'K_3', 'SLS', 'Sales Executive', 'L2', 'SBY', 7_200_000, 'hadi@acme.test'],
            ['Indira Sari', 'F', 'TK_0', 'SLS', 'Account Manager', 'L3', 'HQ', 11_000_000, 'indira@acme.test'],
            ['Joko Widodo', 'M', 'K_1', 'MKT', 'Marketing Manager', 'L5', 'HQ', 13_500_000, 'jokow@acme.test'],
            ['Kartika Dewi', 'F', 'TK_0', 'MKT', 'Digital Marketing Lead', 'L3', 'HQ', 10_500_000, 'kartika@acme.test'],
            ['Lestari Wulandari', 'F', 'K_0', 'MKT', 'Marketing Specialist', 'L2', 'HQ', 7_500_000, 'lestari@acme.test'],
            ['Mahesa Putra', 'M', 'TK_0', 'HRD', 'HR Generalist', 'L2', 'HQ', 7_000_000, 'mahesa@acme.test'],
            ['Nadia Hartono', 'F', 'TK_0', 'HRD', 'Recruiter', 'L2', 'HQ', 7_200_000, 'nadia@acme.test'],
            ['Oka Ramadhan', 'M', 'K_2', 'FIN', 'Finance Staff', 'L2', 'HQ', 7_500_000, 'oka@acme.test'],
            ['Putri Maharani', 'F', 'TK_0', 'FIN', 'Accounting Staff', 'L2', 'HQ', 7_000_000, 'putri@acme.test'],
            ['Qori Hidayat', 'M', 'K_1', 'OPS', 'Operations Staff', 'L2', 'SBY', 6_800_000, 'qori@acme.test'],
            ['Rina Yulianti', 'F', 'TK_0', 'OPS', 'Operations Staff', 'L2', 'SBY', 6_500_000, 'rina@acme.test'],
            ['Setiawan Budi', 'M', 'K_0', 'OPS', 'Operations Manager', 'L5', 'SBY', 13_000_000, 'setiawan@acme.test'],
            ['Tirta Anggara', 'M', 'TK_0', 'ENG', 'Software Engineer', 'L2', 'HQ', 8_000_000, 'tirta@acme.test'],
            ['Umi Kalsum', 'F', 'K_1', 'HRD', 'HR Generalist', 'L2', 'BDG', 7_300_000, 'umi@acme.test'],
            ['Vino Bastian', 'M', 'TK_0', 'SLS', 'Sales Executive', 'L2', 'BDG', 7_100_000, 'vino@acme.test'],
            ['Wati Sukmawati', 'F', 'K_2', 'FIN', 'Finance Staff', 'L2', 'HQ', 7_400_000, 'wati@acme.test'],
            ['Yudha Pranata', 'M', 'TK_0', 'ENG', 'Software Engineer', 'L2', 'BDG', 7_800_000, 'yudha@acme.test'],
            ['Zahra Aulia', 'F', 'TK_0', 'MKT', 'Marketing Specialist', 'L2', 'HQ', 7_300_000, 'zahra@acme.test'],
        ];

        $managerMap = []; // job_level_id => first employee_id created
        $counter = 100;
        $now = now();

        foreach ($employees as [$name, $gender, $ptkp, $deptCode, $jobTitle, $jobLevelCode, $branchCode, $salary, $email]) {
            // Create user account
            $userId = DB::table('users')->insertGetId([
                'tenant_id' => $tenantId,
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $managerId = null;
            if ($jobLevelCode !== 'L5' && $jobLevelCode !== 'L6' && $jobLevelCode !== 'L7') {
                // Find manager (L5+) di same dept
                $managerId = $managerMap[$deptCode] ?? null;
            }

            $joinDate = $now->copy()->subMonths(rand(3, 36))->toDateString();
            $dob = $now->copy()->subYears(rand(24, 50))->subMonths(rand(0, 12))->toDateString();

            $employeeId = DB::table('employees')->insertGetId([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'company_id' => $companyId,
                'branch_id' => $branches[$branchCode] ?? null,
                'department_id' => $depts[$deptCode] ?? null,
                'job_title_id' => $jobTitles[$jobTitle] ?? null,
                'job_level_id' => $jobLevels[$jobLevelCode] ?? null,
                'employee_no' => 'EMP-2026-' . str_pad($counter++, 4, '0', STR_PAD_LEFT),
                'nik' => encrypt(str_pad((string) rand(1000000000000000, 9999999999999999), 16, '0')),
                'npwp' => encrypt(sprintf('%02d.%03d.%03d.%d-%03d.%03d', rand(1, 99), rand(1, 999), rand(1, 999), rand(1, 9), rand(1, 999), 0)),
                'full_name' => $name,
                'nickname' => explode(' ', $name)[0],
                'gender' => $gender,
                'place_of_birth' => collect(['Jakarta', 'Surabaya', 'Bandung', 'Yogyakarta', 'Medan', 'Semarang'])->random(),
                'date_of_birth' => $dob,
                'religion' => collect(['islam', 'christian', 'catholic', 'hindu', 'buddha'])->random(),
                'marital_status' => str_starts_with($ptkp, 'K') ? 'married' : 'single',
                'ptkp_status' => $ptkp,
                'nationality' => 'IDN',
                'phone' => '62812' . rand(10000000, 99999999),
                'employee_email' => $email,
                'join_date' => $joinDate,
                'employment_status' => 'active',
                'employment_type' => 'permanent',
                'manager_employee_id' => $managerId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Store first manager per dept
            if (($jobLevelCode === 'L5' || $jobLevelCode === 'L4') && !isset($managerMap[$deptCode])) {
                $managerMap[$deptCode] = $employeeId;
            }

            // Bank account
            DB::table('employee_bank_accounts')->insert([
                'tenant_id' => $tenantId,
                'employee_id' => $employeeId,
                'bank_name' => collect(['BCA', 'Mandiri', 'BNI', 'BRI'])->random(),
                'account_number' => encrypt((string) rand(1000000000, 9999999999)),
                'account_holder_name' => $name,
                'is_primary' => true,
                'allocation_percentage' => 100,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Contract aktif
            DB::table('contracts')->insert([
                'tenant_id' => $tenantId,
                'employee_id' => $employeeId,
                'contract_type' => 'PKWTT',
                'contract_no' => 'KTR-' . Str::upper(Str::random(8)),
                'start_date' => $joinDate,
                'end_date' => null,
                'salary_base' => $salary,
                'salary_currency' => 'IDR',
                'job_title_id' => $jobTitles[$jobTitle] ?? null,
                'department_id' => $depts[$deptCode] ?? null,
                'branch_id' => $branches[$branchCode] ?? null,
                'working_hours_per_week' => 40,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // BPJS enrollments (5 programs)
            foreach (['kesehatan', 'jht', 'jp', 'jkk', 'jkm'] as $program) {
                DB::table('bpjs_enrollments')->insert([
                    'tenant_id' => $tenantId,
                    'employee_id' => $employeeId,
                    'program' => $program,
                    'bpjs_number' => encrypt((string) rand(1000000000, 9999999999)),
                    'enrolled_at' => $joinDate,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Leave balance untuk tahun ini
            $leaveTypes = DB::table('leave_types')->where('tenant_id', $tenantId)->where('requires_balance', true)->get();
            foreach ($leaveTypes as $lt) {
                DB::table('leave_balances')->insert([
                    'tenant_id' => $tenantId,
                    'employee_id' => $employeeId,
                    'leave_type_id' => $lt->id,
                    'year' => $now->year,
                    'allocated' => $lt->days_per_year ?? 0,
                    'used' => 0,
                    'carried_over' => 0,
                    'pending' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Schedule reguler 30 hari
            $regularShiftId = DB::table('shifts')->where('tenant_id', $tenantId)->where('code', 'REG')->value('id');
            for ($d = 0; $d < 30; $d++) {
                $date = $now->copy()->subDays($d);
                if ($date->isWeekend()) continue;
                DB::table('employee_schedules')->insert([
                    'tenant_id' => $tenantId,
                    'employee_id' => $employeeId,
                    'date' => $date->toDateString(),
                    'shift_id' => $regularShiftId,
                    'source' => 'template',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $this->command->info("Demo: 25 employees seeded + bank account + contracts + BPJS enrollments + leave balances + 30-day schedules.");
    }
}
