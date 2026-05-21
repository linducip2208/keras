<?php

namespace Database\Seeders;

use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Services\Payroll\PayrollCalculationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoPayrollSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = DB::table('tenants')->where('slug', 'acme')->value('id');
        $companyId = DB::table('companies')->where('tenant_id', $tenantId)->value('id');
        if (!$tenantId || !$companyId) return;

        $now = now();
        $period = PayrollPeriod::create([
            'tenant_id' => $tenantId,
            'company_id' => $companyId,
            'name' => $now->copy()->subMonth()->format('Y-m'),
            'period_type' => 'monthly',
            'start_date' => $now->copy()->subMonth()->startOfMonth()->toDateString(),
            'end_date' => $now->copy()->subMonth()->endOfMonth()->toDateString(),
            'cutoff_attendance_date' => $now->copy()->subMonth()->endOfMonth()->subDays(5)->toDateString(),
            'payroll_date' => $now->copy()->subMonth()->endOfMonth()->toDateString(),
            'status' => 'finalized',
        ]);

        $run = PayrollRun::create([
            'tenant_id' => $tenantId,
            'payroll_period_id' => $period->id,
            'run_type' => 'regular',
            'status' => 'draft',
        ]);

        // Force calculate
        $service = app(PayrollCalculationService::class);
        // Bind tenant context
        app()->instance('currentTenant', (object) ['id' => $tenantId]);

        try {
            $service->runForRun($run, $tenantId);
            $this->command->info("Demo: Payroll run #{$run->id} calculated for period {$period->name}. Status: " . $run->fresh()->status);
        } catch (\Throwable $e) {
            $this->command->warn("Payroll seed skipped: " . $e->getMessage());
        } finally {
            app()->forgetInstance('currentTenant');
        }
    }
}
