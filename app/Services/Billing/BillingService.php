<?php

namespace App\Services\Billing;

use App\Adapters\AdapterRegistry;
use App\Models\AdapterConfig;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BillingService
{
    public function __construct(private AdapterRegistry $registry) {}

    /**
     * Create a payment for an invoice, using owner-configured payment adapter.
     */
    public function createPayment(Invoice $invoice, AdapterConfig $adapterConfig, string $paymentMethod = 'auto'): Payment
    {
        $adapter = $this->registry->resolve($adapterConfig->id);

        $payment = Payment::create([
            'tenant_id' => $invoice->tenant_id,
            'invoice_id' => $invoice->id,
            'adapter_config_id' => $adapterConfig->id,
            'reference' => 'PAY-' . Str::upper(Str::random(12)),
            'amount' => $invoice->total_amount,
            'currency' => $invoice->currency,
            'payment_method' => $paymentMethod,
            'status' => 'pending',
        ]);

        $tenant = Tenant::find($invoice->tenant_id);

        $result = $adapter->createCharge([
            'reference' => $payment->reference,
            'amount' => $invoice->total_amount,
            'currency' => $invoice->currency,
            'customer_email' => $tenant->billing_email,
            'customer_name' => $tenant->name,
            'description' => "Invoice {$invoice->number}",
            'success_url' => config('app.url') . "/billing/success/{$payment->reference}",
            'cancel_url' => config('app.url') . "/billing/cancel/{$payment->reference}",
            'webhook_url' => config('app.url') . "/webhook/payment/{$adapterConfig->id}",
        ]);

        $payment->update([
            'external_reference_id' => $result['reference_id'] ?? null,
            'payment_url' => $result['payment_url'] ?? null,
            'qr_string' => $result['qr_string'] ?? null,
            'va_number' => $result['va_number'] ?? null,
            'expires_at' => $result['expires_at'] ?? null,
            'metadata' => $result['raw'] ?? null,
        ]);

        return $payment->fresh();
    }

    /**
     * Process inbound webhook from payment provider.
     */
    public function processWebhook(AdapterConfig $adapterConfig, string $rawBody, array $headers): array
    {
        $adapter = $this->registry->resolve($adapterConfig->id);
        $parsed = $adapter->parseWebhook($rawBody, $headers);

        if (empty($parsed['reference_id'])) {
            return ['ok' => false, 'message' => 'No reference_id in payload'];
        }

        $payment = Payment::where('external_reference_id', $parsed['reference_id'])
            ->orWhere('reference', $parsed['reference_id'])
            ->first();

        if (!$payment) {
            return ['ok' => false, 'message' => 'Payment not found for reference ' . $parsed['reference_id']];
        }

        DB::transaction(function () use ($payment, $parsed) {
            $payment->update([
                'status' => $parsed['status'],
                'webhook_payload' => $parsed['raw'] ?? null,
                'paid_at' => $parsed['status'] === 'paid' ? now() : $payment->paid_at,
            ]);

            if ($parsed['status'] === 'paid' && $payment->invoice_id) {
                $invoice = Invoice::find($payment->invoice_id);
                if ($invoice && $invoice->status !== 'paid') {
                    $invoice->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                        'paid_via' => $payment->payment_method,
                    ]);
                    $this->extendSubscription($invoice);
                }
            }
        });

        return ['ok' => true, 'status' => $parsed['status']];
    }

    /**
     * Generate next billing invoice for active subscription.
     */
    public function generateNextInvoice(Subscription $subscription): Invoice
    {
        $amount = $subscription->billing_cycle === 'annual'
            ? $subscription->plan->price_annual
            : $subscription->plan->price_monthly;

        $taxableAmount = $amount * $subscription->seats;
        $tax = $taxableAmount * 0.11; // PPN 11%

        return Invoice::create([
            'tenant_id' => $subscription->tenant_id,
            'subscription_id' => $subscription->id,
            'number' => 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
            'period_start' => $subscription->current_period_end->copy()->addDay(),
            'period_end' => $subscription->billing_cycle === 'annual'
                ? $subscription->current_period_end->copy()->addYear()
                : $subscription->current_period_end->copy()->addMonth(),
            'issue_date' => now(),
            'due_date' => now()->addDays(7),
            'subtotal' => $taxableAmount,
            'tax_amount' => $tax,
            'total_amount' => $taxableAmount + $tax,
            'currency' => $subscription->plan->currency,
            'status' => 'open',
            'line_items' => [
                [
                    'description' => "{$subscription->plan->name} ({$subscription->billing_cycle}, {$subscription->seats} seats)",
                    'quantity' => $subscription->seats,
                    'unit_price' => $amount,
                    'amount' => $taxableAmount,
                ],
                [
                    'description' => 'PPN 11%',
                    'amount' => $tax,
                ],
            ],
        ]);
    }

    private function extendSubscription(Invoice $invoice): void
    {
        if (!$invoice->subscription_id) return;
        $subscription = Subscription::find($invoice->subscription_id);
        if (!$subscription) return;

        $subscription->update([
            'status' => 'active',
            'current_period_start' => $invoice->period_start,
            'current_period_end' => $invoice->period_end,
        ]);
    }
}
