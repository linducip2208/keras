<?php

namespace App\Console\Commands;

use App\Mail\PayslipMail;
use App\Models\Payslip;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailPayslip extends Command
{
    protected $signature = 'payslip:test-email {employee_email?}';
    protected $description = 'Test send payslip email — log driver default, ganti MAIL_MAILER untuk real send';

    public function handle(): int
    {
        $payslip = Payslip::with('employee')->latest()->first();
        if (!$payslip) {
            $this->error('No payslip exists. Generate dulu via PayslipPdfService::generateBulk()');
            return self::FAILURE;
        }

        $targetEmail = $this->argument('employee_email') ?? $payslip->employee->personal_email ?? $payslip->employee->employee_email;
        if (!$targetEmail) {
            $this->error("Employee {$payslip->employee->full_name} tidak punya email");
            return self::FAILURE;
        }

        $this->info("Sending PayslipMail for {$payslip->slip_number} → {$targetEmail}");
        $this->info("MAIL_MAILER: " . config('mail.default'));

        try {
            Mail::to($targetEmail)->send(new PayslipMail($payslip));
            $this->info('✅ Sent! (cek storage/logs/laravel.log kalau MAIL_MAILER=log)');
            if (config('mail.default') === 'log') {
                $this->warn('Note: MAIL_MAILER=log artinya email cuma di-write ke storage/logs/laravel.log');
                $this->warn('Untuk real send, ganti ke smtp/mailgun/ses/sendgrid di .env');
            }
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
