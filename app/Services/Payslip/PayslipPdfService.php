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

        $pdf->setOption('isRemoteEnabled', false);
        // KEJUJURAN FITUR: DomPDF v3+ tidak mendukung enkripsi PDF, sehingga
        // password di bawah TIDAK diterapkan ke file. PDF disimpan di storage
        // privat (disk local) dan hanya diakses via download terautentikasi.
        // Enkripsi PDF butuh post-process qpdf — lihat docs/TECHNICAL-DEBT.md.
        // password_hint sengaja dikosongkan agar tidak menyesatkan user.

        $path = "payslips/{$item->employee_id}/" . now()->format('Y/m') . "/{$slipNumber}.pdf";
        Storage::disk('local')->put($path, $pdf->output());

        $existing = Payslip::where('payroll_item_id', $item->id)->first();
        $attributes = [
            'tenant_id' => $item->tenant_id,
            'payroll_item_id' => $item->id,
            'employee_id' => $item->employee_id,
            'slip_number' => $slipNumber,
            'slip_date' => now()->toDateString(),
            'pdf_path' => $path,
            'password_hint' => null,
        ];

        if ($existing) {
            $existing->update($attributes);
            return $existing;
        }
        return Payslip::create($attributes);
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
        // Nomor slip mengikuti periode payroll (YYYYMM), bukan tanggal generate,
        // agar backpay/koreksi tidak salah label.
        $periodLabel = '000000';
        try {
            $period = $item->run?->period;
            $refDate = $period?->payroll_date ?? $period?->end_date;
            if ($refDate) {
                $periodLabel = $refDate->format('Ym');
            }
        } catch (\Throwable) {
            // fallback ke format default di bawah
        }
        return sprintf('SLIP-%s-%05d', $periodLabel, $item->id);
    }
}
