<?php

namespace App\Mail;

use App\Http\Controllers\Web\BillingController;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
        $this->onQueue('mail');
    }

    public function envelope(): Envelope
    {
        $this->invoice->loadMissing('tenant');
        $to = $this->invoice->tenant->billing_email ?: 'billing@' . $this->invoice->tenant->slug . '.test';

        $statusLabel = match ($this->invoice->status) {
            'open' => 'Invoice Baru',
            'past_due' => '⚠️ Invoice Jatuh Tempo',
            'paid' => '✅ Receipt — Invoice Lunas',
            default => 'Invoice',
        };

        return new Envelope(
            subject: "{$statusLabel} {$this->invoice->number} — Rp " . number_format($this->invoice->total_amount, 0, ',', '.'),
            to: [$to],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.invoice',
            with: [
                'invoice' => $this->invoice,
                'tenant' => $this->invoice->tenant,
                'payUrl' => config('app.url') . '/billing/pay/' . BillingController::tokenFor($this->invoice),
            ],
        );
    }
}
