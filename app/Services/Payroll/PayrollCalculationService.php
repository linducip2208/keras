<?php

namespace App\Services\Payroll;

use App\Models\Employee;
use App\Models\PayrollComponent;
use App\Models\PayrollItem;
use App\Models\PayrollItemComponent;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Models\TaxCalculation;
use App\Models\BPJSCalculation;
use App\Services\BPJS\BPJSCalculator;
use App\Services\Tax\PPh21TerCalculator;
use Illuminate\Support\Facades\DB;

class PayrollCalculationService
{
    public function __construct(
        private PPh21TerCalculator $taxCalc,
        private BPJSCalculator $bpjsCalc,
    ) {}

    public function runForRun(PayrollRun $run, int $tenantId): void
    {
        DB::transaction(function () use ($run, $tenantId) {
            $period = PayrollPeriod::where('tenant_id', $tenantId)->find($run->payroll_period_id);
            abort_if(!$period, 422, 'Periode payroll tidak ditemukan untuk tenant ini.');

            // Tahun regulasi (TER/BPJS/PTKP) mengikuti tanggal periode — bukan now().
            $refDate = $period->payroll_date ?? $period->end_date ?? now();
            $regYear = (int) $refDate->format('Y');
            $this->taxCalc->setYear($regYear);
            $this->bpjsCalc->setYear($regYear);

            $employees = Employee::query()
                ->where('tenant_id', $tenantId)
                ->where('company_id', $period->company_id)
                ->whereIn('employment_status', ['active', 'probation'])
                ->get();

            $run->update(['status' => 'calculating', 'started_at' => now(), 'total_employees' => $employees->count()]);

            $components = PayrollComponent::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('display_order')->get();

            $totals = ['gross' => 0, 'net' => 0, 'tax' => 0, 'bpjs_employer' => 0, 'bpjs_employee' => 0];

            foreach ($employees as $employee) {
                $result = $this->calculateForEmployee($employee, $run, $components, $tenantId, $period);
                $totals['gross'] += $result['gross'];
                $totals['net'] += $result['net_pay'];
                $totals['tax'] += $result['pph_21'];
                $totals['bpjs_employer'] += $result['bpjs_employer'];
                $totals['bpjs_employee'] += $result['bpjs_employee'];
            }

            $run->update([
                'status' => 'review',
                'completed_at' => now(),
                'total_gross' => $totals['gross'],
                'total_net' => $totals['net'],
                'total_tax' => $totals['tax'],
                'total_bpjs_employer' => $totals['bpjs_employer'],
                'total_bpjs_employee' => $totals['bpjs_employee'],
            ]);
        });
    }

    private function calculateForEmployee(Employee $employee, PayrollRun $run, $components, int $tenantId, PayrollPeriod $period): array
    {
        // Basic: ambil gaji pokok dari kontrak aktif
        $contract = $employee->contracts()->where('status', 'active')->latest()->first();
        $basicSalary = $contract ? (float) $contract->salary_base : 0;

        $earnings = 0;
        $deductions = 0;
        $bpjsBasis = $basicSalary;
        $taxableGross = 0;

        $item = PayrollItem::create([
            'tenant_id' => $tenantId,
            'payroll_run_id' => $run->id,
            'employee_id' => $employee->id,
            'basic_salary' => $basicSalary,
            'status' => 'pending',
        ]);

        // Iterate components
        foreach ($components as $comp) {
            $amount = $this->calculateComponentAmount($comp, $employee, $basicSalary);
            if ($amount == 0) continue;

            PayrollItemComponent::create([
                'tenant_id' => $tenantId,
                'payroll_item_id' => $item->id,
                'payroll_component_id' => $comp->id,
                'amount' => $amount,
                'is_taxable' => $comp->is_taxable,
                'is_bpjs_basis' => $comp->is_bpjs_basis,
            ]);

            if ($comp->type === 'earning') {
                $earnings += $amount;
                if ($comp->is_taxable) $taxableGross += $amount;
                if ($comp->is_bpjs_basis) $bpjsBasis += ($comp->code === 'GAPOK' ? 0 : $amount); // gapok sudah di base
            } elseif ($comp->type === 'deduction') {
                $deductions += $amount;
            }
        }

        // Hitung BPJS dari basis BPJS (gapok + komponen ber-flag bpjs).
        $bpjsResult = $this->bpjsCalc->calculateAll($bpjsBasis);

        // Hitung PPh 21 TER (PMK 168/2023): tarif efektif dikenakan LANGSUNG
        // ke penghasilan bruto sebulan (tanpa pengurang biaya jabatan / iuran).
        // Biaya jabatan (5%, maks 500rb/bln) + iuran karyawan hanya dipakai
        // pada koreksi progresif tahunan (Masa Pajak Terakhir / Desember).
        $taxResult = $this->taxCalc->calculate($taxableGross, $employee->ptkp_status);
        $jobExpenseInfo = min($taxableGross * 0.05, 500_000);

        // Final net
        $netPay = $earnings - $deductions - $taxResult['pph_21'] - $bpjsResult['total_employee'];

        $item->update([
            'gross' => $earnings,
            'total_earnings' => $earnings,
            'total_deductions' => $deductions,
            'pph_21' => $taxResult['pph_21'],
            'bpjs_employer' => $bpjsResult['total_employer'],
            'bpjs_employee' => $bpjsResult['total_employee'],
            'net_pay' => $netPay,
            'status' => 'calculated',
            'calculated_at' => now(),
        ]);

        // Simpan tax_calculations. gross_basis = bruto kena TER (bulanan);
        // periode mengikuti tanggal payroll, bukan now().
        $refDate = $period->payroll_date ?? $period->end_date ?? now();
        TaxCalculation::create([
            'tenant_id' => $tenantId,
            'payroll_item_id' => $item->id,
            'employee_id' => $employee->id,
            'period_year' => (int) $refDate->format('Y'),
            'period_month' => (int) $refDate->format('n'),
            'gross_basis' => $taxableGross,
            'ptkp_amount' => $this->taxCalc->annualPtkp($employee->ptkp_status),
            'ter_category' => $taxResult['ter_category'],
            'ter_rate' => $taxResult['ter_rate'],
            'pph_21_amount' => $taxResult['pph_21'],
            'method_used' => 'ter',
            'calculation_breakdown' => [
                'note' => $taxResult['breakdown'],
                'job_expense_monthly_info' => $jobExpenseInfo,
                'bpjs_employee' => $bpjsResult['total_employee'],
            ],
        ]);

        // Simpan bpjs_calculations
        foreach ($bpjsResult['breakdown'] as $program => $data) {
            BPJSCalculation::create([
                'tenant_id' => $tenantId,
                'payroll_item_id' => $item->id,
                'employee_id' => $employee->id,
                'program' => $program,
                'basis_amount' => $data['basis'],
                'employer_amount' => $data['employer_amount'],
                'employee_amount' => $data['employee_amount'],
            ]);
        }

        return [
            'gross' => $earnings,
            'net_pay' => $netPay,
            'pph_21' => $taxResult['pph_21'],
            'bpjs_employer' => $bpjsResult['total_employer'],
            'bpjs_employee' => $bpjsResult['total_employee'],
        ];
    }

    private function calculateComponentAmount(PayrollComponent $comp, Employee $employee, float $basicSalary): float
    {
        return match ($comp->calculation) {
            'fixed' => $comp->code === 'GAPOK' ? $basicSalary : (float) $comp->default_amount,
            'formula' => $this->evalFormula($comp->formula, ['gaji_pokok' => $basicSalary]),
            'attendance_based' => (float) $comp->default_amount * 22, // assume 22 work days
            'manual' => 0,
            default => 0,
        };
    }

    private function evalFormula(?string $formula, array $vars): float
    {
        if (!$formula) return 0;
        // VERY simple eval — production: use expression parser library
        $expr = $formula;
        foreach ($vars as $k => $v) {
            $expr = str_replace($k, (string)$v, $expr);
        }
        if (!preg_match('/^[\d\s\+\-\*\/\.\(\)]+$/', $expr)) return 0;
        try {
            return (float) eval("return $expr;");
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
