<?php

namespace App\Services\Payslip;

use App\Models\Employee;
use App\Models\Payslip;
use App\Models\PayrollItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PayslipPdfService
{
    /**
     * Generate PDF slip gaji untuk satu PayrollItem.
     * Password locked dengan NIK karyawan (default) atau DOB format ddmmyyyy.
     */
    public function generate(PayrollItem $item, string $passwordMode = 'nik'): Payslip
    {
        $item->load(['employee', 'components.component']);
        $employee = $item->employee;

        $password = $this->resolvePassword($employee, $passwordMode);
        $slipNumber = $this->generateSlipNumber($item);

        $pdf = Pdf::loadView('pdfs.payslip', [
            'item' => $item,
            'employee' => $employee,
            'company' => $employee->company,
            'slipNumber' => $slipNumber,
            'periodLabel' => $item->run->period->name ?? now()->format('Y-m'),
            'earnings' => $item->components->filter(fn ($c) => $c->component->type === 'earning'),
            'deductions' => $item->components->filter(fn ($c) => $c->component->type === 'deduction'),
        ])->setPaper('A4');

        // Set password (owner + user). DomPDF uses dompdf options.
        $pdf->setOption('isRemoteEnabled', false);
        $pdf->getDomPDF()->setEncryption($password, $password, [
            'print', 'copy', 'modify',
        ]);

        $path = "payslips/{$item->employee_id}/" . now()->format('Y/m') . "/{$slipNumber}.pdf";
        Storage::disk('local')->put($path, $pdf->output());

        return Payslip::updateOrCreate(
            ['payroll_item_id' => $item->id],
            [
                'tenant_id' => $item->tenant_id,
                'employee_id' => $item->employee_id,
                'slip_number' => $slipNumber,
                'slip_date' => now()->toDateString(),
                'pdf_path' => $path,
                'password_hint' => $passwordMode === 'nik' ? 'Gunakan NIK Anda' : 'Tanggal lahir format ddmmyyyy',
            ],
        );
    }

    /**
     * Bulk generate untuk satu PayrollRun (untuk dispatch ke queue).
     */
    public function generateBulk(int $payrollRunId): int
    {
        $items = PayrollItem::where('payroll_run_id', $payrollRunId)
            ->where('status', 'calculated')
            ->get();

        $count = 0;
        foreach ($items as $item) {
            try {
                $this->generate($item);
                $count++;
            } catch (\Throwable $e) {
                logger()->error("Payslip generate failed for item {$item->id}: " . $e->getMessage());
            }
        }
        return $count;
    }

    private function resolvePassword(Employee $employee, string $mode): string
    {
        return match ($mode) {
            'nik' => $employee->nik ?: 'SLIP' . $employee->id,
            'dob' => $employee->date_of_birth?->format('dmY') ?: 'SLIP' . $employee->id,
            default => 'SLIP' . $employee->id,
        };
    }

    private function generateSlipNumber(PayrollItem $item): string
    {
        return sprintf(
            'SLIP/%s/%s/%05d',
            now()->format('Y'),
            now()->format('m'),
            $item->id,
        );
    }
}
