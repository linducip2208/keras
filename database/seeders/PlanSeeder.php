<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['starter', 'Starter', 'Untuk startup <25 karyawan', 25, 10_000, 102_000, ['Employee DB','Attendance GPS+Selfie','Payroll PPh21 TER','BPJS 5 program','Leave + Overtime','Mobile app']],
            ['basic', 'Basic', 'UKM 26-100 karyawan', 100, 15_000, 153_000, ['Semua Starter','Reimbursement','Letter Generator','Bank Disburse (5 bank)','WhatsApp Bot ESS']],
            ['essential', 'Essential', 'Mid-market 101-500 karyawan', 500, 25_000, 255_000, ['Semua Basic','Recruitment ATS','OKR & 360 Appraisal','LMS + Asset + Travel','Analytics + AI Insight','Dedicated AM']],
            ['suite', 'Suite', 'Enterprise / Group >500', null, 40_000, 408_000, ['Semua Essential','Multi-company group','DJP Coretax direct','SIPP API direct','Predictive turnover','Compensation benchmark','SLA 99.9%']],
        ];

        foreach ($plans as $i => [$slug, $name, $desc, $max, $monthly, $annual, $features]) {
            DB::table('plans')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'description' => $desc,
                    'max_employees' => $max,
                    'price_monthly' => $monthly,
                    'price_annual' => $annual,
                    'currency' => 'IDR',
                    'features' => json_encode($features),
                    'is_active' => true,
                    'display_order' => $i + 1,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
