<?php

namespace App\Mail;

use App\Models\Payslip;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PayslipMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Payslip $payslip) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Slip Gaji {$this->payslip->slip_number}",
            to: [$this->payslip->employee->personal_email ?: $this->payslip->employee->employee_email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.payslip',
            with: [
                'employee' => $this->payslip->employee,
                'slipNumber' => $this->payslip->slip_number,
                'passwordHint' => $this->payslip->password_hint,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromStorageDisk('local', $this->payslip->pdf_path)
                ->as("SlipGaji-{$this->payslip->slip_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
