<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoTenantSeeder extends Seeder
{
    public function run(): void
    {
        // Tenant
        $tenantId = DB::table('tenants')->insertGetId([
            'slug' => 'acme',
            'name' => 'Acme Indonesia',
            'subdomain' => 'acme',
            'plan' => 'essential',
            'status' => 'active',
            'country' => 'ID',
            'timezone' => 'Asia/Jakarta',
            'locale' => 'id',
            'currency' => 'IDR',
            'billing_email' => 'billing@acme.test',
            'settings' => json_encode(['theme' => 'default']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Users
        $users = [
            ['owner@hris.test', 'Owner Acme', 'super_admin', true],
            ['hr@hris.test', 'HR Manager', 'hr_admin', false],
            ['hrstaff@hris.test', 'HR Staff', 'hr_staff', false],
            ['finance@hris.test', 'Finance Manager', 'finance', false],
            ['manager-sales@hris.test', 'Manager Sales', 'manager', false],
            ['manager-tech@hris.test', 'Manager Tech', 'manager', false],
            ['employee@hris.test', 'Karyawan Office', 'employee', false],
            ['field@hris.test', 'Karyawan Field', 'employee', false],
        ];

        $userIds = [];
        foreach ($users as [$email, $name, $role, $superAdmin]) {
            $userIds[$email] = DB::table('users')->insertGetId([
                'tenant_id' => $tenantId,
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'is_super_admin' => $superAdmin,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update tenant owner
        DB::table('tenants')->where('id', $tenantId)->update(['owner_user_id' => $userIds['owner@hris.test']]);

        // Company
        $companyId = DB::table('companies')->insertGetId([
            'tenant_id' => $tenantId,
            'name' => 'PT Acme Indonesia',
            'legal_name' => 'PT Acme Indonesia Sejahtera',
            'npwp' => '01.234.567.8-901.000',
            'address' => 'Jl. Sudirman No. 1, Jakarta Pusat',
            'phone' => '021-5551234',
            'email' => 'info@acme.co.id',
            'country' => 'ID',
            'timezone' => 'Asia/Jakarta',
            'currency' => 'IDR',
            'fiscal_year_start_month' => 1,
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Branches
        $branches = [
            ['HQ', 'Kantor Pusat Jakarta', 'Jl. Sudirman No. 1, Jakarta Pusat', -6.2088, 106.8456, true],
            ['SBY', 'Cabang Surabaya', 'Jl. Pemuda No. 50, Surabaya', -7.2575, 112.7521, false],
            ['BDG', 'Cabang Bandung', 'Jl. Asia Afrika No. 100, Bandung', -6.9175, 107.6191, false],
        ];
        foreach ($branches as [$code, $name, $address, $lat, $lng, $default]) {
            DB::table('branches')->insert([
                'tenant_id' => $tenantId,
                'company_id' => $companyId,
                'code' => $code,
                'name' => $name,
                'address' => $address,
                'latitude' => $lat,
                'longitude' => $lng,
                'geofence_radius_meters' => 150,
                'working_hours' => json_encode([
                    'mon' => '09:00-18:00', 'tue' => '09:00-18:00', 'wed' => '09:00-18:00',
                    'thu' => '09:00-18:00', 'fri' => '09:00-18:00',
                ]),
                'umk_amount' => 5_067_381, // Jakarta UMK 2026 approx
                'is_default' => $default,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Departments
        $depts = [
            ['ENG', 'Engineering', 'CC-ENG-001'],
            ['SLS', 'Sales', 'CC-SLS-001'],
            ['MKT', 'Marketing', 'CC-MKT-001'],
            ['HRD', 'Human Resources', 'CC-HRD-001'],
            ['FIN', 'Finance', 'CC-FIN-001'],
            ['OPS', 'Operations', 'CC-OPS-001'],
        ];
        foreach ($depts as [$code, $name, $cc]) {
            DB::table('departments')->insert([
                'tenant_id' => $tenantId,
                'company_id' => $companyId,
                'code' => $code,
                'name' => $name,
                'cost_center_code' => $cc,
                'headcount_budget' => 50,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Job Levels
        $levels = [
            ['L1', 'Junior', 4_500_000, 6_500_000],
            ['L2', 'Mid', 6_500_000, 10_000_000],
            ['L3', 'Senior', 10_000_000, 18_000_000],
            ['L4', 'Lead', 18_000_000, 30_000_000],
            ['L5', 'Manager', 25_000_000, 45_000_000],
            ['L6', 'Director', 45_000_000, 80_000_000],
            ['L7', 'C-Level', 80_000_000, 200_000_000],
        ];
        foreach ($levels as $i => [$code, $name, $min, $max]) {
            DB::table('job_levels')->insert([
                'tenant_id' => $tenantId,
                'code' => $code,
                'name' => $name,
                'salary_min' => $min,
                'salary_max' => $max,
                'order' => $i + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Job Titles
        $jobTitles = [
            'Software Engineer', 'Senior Software Engineer', 'Engineering Manager',
            'Sales Executive', 'Sales Manager', 'Account Manager',
            'Marketing Specialist', 'Digital Marketing Lead', 'Marketing Manager',
            'HR Generalist', 'HR Manager', 'Recruiter',
            'Finance Staff', 'Accounting Staff', 'Finance Manager',
            'Operations Staff', 'Operations Manager',
            'CEO', 'CTO', 'CFO', 'COO',
        ];
        foreach ($jobTitles as $title) {
            DB::table('job_titles')->insert([
                'tenant_id' => $tenantId,
                'name' => $title,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Shifts
        $shifts = [
            ['REG', 'Reguler 9-18', '09:00:00', '18:00:00', 60, 8],
            ['PAGI', 'Shift Pagi 06-14', '06:00:00', '14:00:00', 60, 8],
            ['SIANG', 'Shift Siang 14-22', '14:00:00', '22:00:00', 60, 8],
            ['MALAM', 'Shift Malam 22-06', '22:00:00', '06:00:00', 60, 8],
            ['HALF', 'Setengah Hari', '09:00:00', '13:00:00', 0, 4],
        ];
        foreach ($shifts as [$code, $name, $start, $end, $break, $hours]) {
            DB::table('shifts')->insert([
                'tenant_id' => $tenantId,
                'code' => $code,
                'name' => $name,
                'start_time' => $start,
                'end_time' => $end,
                'break_minutes' => $break,
                'working_hours' => $hours,
                'crosses_midnight' => $code === 'MALAM',
                'allowed_late_minutes' => 15,
                'allowed_early_leave_minutes' => 0,
                'color' => '#3b82f6',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Leave Types
        $leaveTypes = [
            ['CT', 'Cuti Tahunan', 12, true, false, true, 12, true],
            ['SK', 'Cuti Sakit', null, true, true, false, null, false],
            ['MEL', 'Cuti Melahirkan', 90, true, true, false, null, false],
            ['AYAH', 'Cuti Ayah', 2, true, false, false, null, false],
            ['NIKAH', 'Cuti Menikah', 3, true, false, false, null, false],
            ['KHU', 'Cuti Khusus (Duka)', 2, true, false, false, null, false],
            ['UN', 'Unpaid Leave', null, false, false, false, null, false],
            ['UT', 'Ulang Tahun (Perk)', 1, true, false, false, null, false],
        ];
        foreach ($leaveTypes as [$code, $name, $days, $paid, $attach, $bal, $coMax, $co]) {
            DB::table('leave_types')->insert([
                'tenant_id' => $tenantId,
                'code' => $code,
                'name' => $name,
                'days_per_year' => $days,
                'is_paid' => $paid,
                'requires_attachment' => $attach,
                'requires_balance' => $bal,
                'min_notice_days' => $code === 'CT' ? 3 : 0,
                'can_be_half_day' => $code === 'CT',
                'can_carry_over' => $co,
                'carry_over_max_days' => $coMax,
                'carry_over_expires_in_months' => $co ? 3 : null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Payroll Components
        $components = [
            ['GAPOK', 'Gaji Pokok', 'earning', 'fixed', 0, true, true, true, true],
            ['TR-JBT', 'Tunjangan Jabatan', 'earning', 'fixed', 0, true, true, true, true],
            ['TR-MKN', 'Tunjangan Makan', 'earning', 'attendance_based', 25000, true, false, false, false],
            ['TR-TRP', 'Tunjangan Transport', 'earning', 'attendance_based', 30000, true, false, false, false],
            ['LMB', 'Lembur', 'earning', 'manual', 0, true, false, false, false],
            ['BONUS', 'Bonus', 'earning', 'manual', 0, true, false, false, false],
            ['THR', 'THR', 'earning', 'manual', 0, true, false, false, false],
            ['BPJS-K-EMP', 'BPJS Kesehatan (Karyawan)', 'deduction', 'fixed', 0, false, false, false, false],
            ['BPJS-JHT-EMP', 'BPJS JHT (Karyawan)', 'deduction', 'fixed', 0, false, false, false, false],
            ['BPJS-JP-EMP', 'BPJS JP (Karyawan)', 'deduction', 'fixed', 0, false, false, false, false],
            ['PPH21', 'PPh 21', 'deduction', 'fixed', 0, false, false, false, false],
            ['PINJ', 'Cicilan Pinjaman', 'deduction', 'manual', 0, false, false, false, false],
            ['UNPAID', 'Unpaid Leave Deduction', 'deduction', 'manual', 0, false, false, false, false],
        ];
        foreach ($components as $i => [$code, $name, $type, $calc, $amt, $tax, $bpjs, $thr, $pen]) {
            DB::table('payroll_components')->insert([
                'tenant_id' => $tenantId,
                'code' => $code,
                'name' => $name,
                'type' => $type,
                'calculation' => $calc,
                'default_amount' => $amt,
                'is_taxable' => $tax,
                'is_bpjs_basis' => $bpjs,
                'is_thr_basis' => $thr,
                'is_pension_basis' => $pen,
                'display_order' => $i + 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tax Settings
        DB::table('tax_settings')->insert([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'npwp' => '01.234.567.8-901.000',
            'klu_code' => '62015',
            'calculation_method' => 'gross',
            'ptkp_year' => 2026,
            'ter_year' => 2026,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // BPJS Company Profile
        DB::table('bpjs_company_profiles')->insert([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'npp_bpjs_kesehatan' => '12345678',
            'npp_bpjs_naker' => '87654321',
            'jkk_risk_level' => 'low',
            'jkk_rate' => 0.0054,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Demo tenant + 8 users + 1 company + 3 branches + 6 depts + 7 job levels + 21 job titles + 5 shifts + 8 leave types + 13 payroll components seeded.');
        $this->command->info('Login: owner@hris.test / password');
    }
}
