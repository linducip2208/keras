<?php

namespace App\Services\Tax;

use App\Models\Employee;
use App\Models\TaxCalculation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

/**
 * Bukti Potong PPh 21 — Form 1721-A1.
 * Generated per karyawan per tahun untuk karyawan tetap.
 * Karyawan pakai ini untuk SPT Tahunan personal.
 */
class TaxForm1721A1Service
{
    public function generate(Employee $employee, int $year): string
    {
        $calculations = TaxCalculation::where('employee_id', $employee->id)
            ->where('period_year', $year)
            ->orderBy('period_month')
            ->get();

        $annualGross = $calculations->sum('gross_basis');
        $annualTax = $calculations->sum('pph_21_amount');

        $employee->loadMissing(['company', 'jobTitle', 'department']);

        $data = [
            'employee' => $employee,
            'company' => $employee->company,
            'year' => $year,
            'calculations' => $calculations,
            'annual_gross' => $annualGross,
            'annual_tax' => $annualTax,
            'ptkp_status' => str_replace('_', '/', $employee->ptkp_status ?? 'TK_0'),
            'form_no' => sprintf('1721-A1/%s/%05d', $year, $employee->id),
        ];

        $pdf = Pdf::loadView('pdfs.tax-1721-a1', $data)->setPaper('A4');

        $path = "tax-forms/1721-a1/{$year}/{$employee->id}.pdf";
        Storage::disk('local')->put($path, $pdf->output());

        return $path;
    }

    public function generateBulk(int $tenantId, int $year): int
    {
        $employees = Employee::where('tenant_id', $tenantId)->get();
        $count = 0;
        foreach ($employees as $emp) {
            $hasCalc = TaxCalculation::where('employee_id', $emp->id)
                ->where('period_year', $year)->exists();
            if ($hasCalc) {
                $this->generate($emp, $year);
                $count++;
            }
        }
        return $count;
    }
}
