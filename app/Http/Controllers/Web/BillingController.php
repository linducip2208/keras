<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AdapterConfig;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\Billing\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BillingController extends Controller
{
    public function __construct(private BillingService $billing) {}

    /**
     * Show invoice + pilihan payment method.
     * Token = signed identifier untuk akses tanpa login (link di email).
     */
    public function pay(Request $request, string $token)
    {
        $invoice = $this->resolveInvoice($token);
        $invoice->load(['tenant', 'subscription.plan']);

        $methods = $this->groupedAdapters($invoice->tenant_id);

        return view('billing.pay', [
            'invoice' => $invoice,
            'methods' => $methods,
            'token' => $token,
            'title' => "Bayar Invoice {$invoice->number} — Rp " . number_format($invoice->total_amount, 0, ',', '.'),
            'description' => "Pembayaran invoice HRIS Indonesia untuk {$invoice->tenant->name}.",
        ]);
    }

    /**
     * Process payment method selection — create Payment + redirect/show.
     */
    public function process(Request $request, string $token)
    {
        $invoice = $this->resolveInvoice($token);
        if ($invoice->status === 'paid') {
            return redirect()->route('billing.success', $token);
        }

        $data = $request->validate([
            'adapter_config_id' => 'required|integer',
            'payment_method' => 'required|string',
            'channel_code' => 'nullable|string',
        ]);

        // Tenant-scoped: gateway milik tenant lain tidak bisa dipakai.
        $adapter = AdapterConfig::where('tenant_id', $invoice->tenant_id)
            ->where('is_active', true)
            ->findOrFail($data['adapter_config_id']);

        $payment = $this->billing->createPayment($invoice, $adapter, $data['payment_method']);

        // Branch per result type
        if ($payment->payment_url) {
            return redirect($payment->payment_url);
        }
        if ($payment->qr_string || $payment->va_number) {
            return redirect()->route('billing.instruction', ['token' => $token, 'payment' => $payment->reference]);
        }

        return redirect()->route('billing.success', $token);
    }

    public function instruction(Request $request, string $token, string $payment)
    {
        $invoice = $this->resolveInvoice($token);
        $paymentRow = Payment::where('reference', $payment)->where('invoice_id', $invoice->id)->firstOrFail();

        return view('billing.instruction', [
            'invoice' => $invoice,
            'payment' => $paymentRow,
            'title' => "Instruksi Pembayaran — {$invoice->number}",
        ]);
    }

    public function success(string $token)
    {
        $invoice = $this->resolveInvoice($token);
        return view('billing.success', [
            'invoice' => $invoice,
            'title' => "Pembayaran Berhasil — {$invoice->number}",
        ]);
    }

    public function cancel(string $token)
    {
        $invoice = $this->resolveInvoice($token);
        return view('billing.cancel', [
            'invoice' => $invoice,
            'title' => "Pembayaran Dibatalkan — {$invoice->number}",
        ]);
    }

    private function resolveInvoice(string $token): Invoice
    {
        // Token format: INV-{id}-{hash} (signed dengan APP_KEY)
        if (!Str::startsWith($token, 'INV-')) abort(404);

        $parts = explode('-', $token);
        $id = (int) ($parts[1] ?? 0);
        $hash = $parts[2] ?? '';

        $invoice = Invoice::findOrFail($id);
        $expected = substr(hash_hmac('sha256', "inv:{$invoice->id}:{$invoice->number}", config('app.key')), 0, 12);

        if (!hash_equals($expected, $hash)) {
            abort(404);
        }
        return $invoice;
    }

    public static function tokenFor(Invoice $invoice): string
    {
        $hash = substr(hash_hmac('sha256', "inv:{$invoice->id}:{$invoice->number}", config('app.key')), 0, 12);
        return "INV-{$invoice->id}-{$hash}";
    }

    private function groupedAdapters(int $tenantId): array
    {
        $adapters = AdapterConfig::where('tenant_id', $tenantId)
            ->where('domain', 'payment')
            ->where('is_active', true)
            ->get();

        $groups = [
            'redirect' => ['title' => '💳 Kartu / Hosted Checkout', 'icon' => '💳', 'items' => []],
            'qris' => ['title' => '📱 QRIS (Semua E-Wallet ID)', 'icon' => '📱', 'items' => []],
            'va' => ['title' => '🏦 Virtual Account', 'icon' => '🏦', 'items' => []],
            'ewallet' => ['title' => '💸 E-Wallet (DANA/OVO/GoPay)', 'icon' => '💸', 'items' => []],
            'paypal' => ['title' => '🌍 PayPal (International)', 'icon' => '🌍', 'items' => []],
            'manual' => ['title' => '🧾 Transfer Manual', 'icon' => '🧾', 'items' => []],
        ];

        foreach ($adapters as $a) {
            $bucket = match (true) {
                str_starts_with($a->format, 'redirect_') || $a->format === 'redirect_checkout' || str_starts_with($a->format, 'embed_') => 'redirect',
                str_starts_with($a->format, 'qris') => 'qris',
                str_starts_with($a->format, 'va') => 'va',
                str_starts_with($a->format, 'ewallet') => 'ewallet',
                $a->format === 'paypal' => 'paypal',
                $a->format === 'manual_bank_transfer' => 'manual',
                default => 'redirect',
            };
            $groups[$bucket]['items'][] = $a;
        }

        return array_filter($groups, fn ($g) => !empty($g['items']));
    }
}
