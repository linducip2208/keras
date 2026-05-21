<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>{{ $invoice->number }}</title>
<style>
    body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; max-width: 640px; margin: 0 auto; padding: 24px; color: #1a1a1a; line-height: 1.6; background: #faf5ff; }
    .card { background: white; border-radius: 16px; padding: 32px; box-shadow: 0 8px 32px -4px rgba(236, 72, 153, 0.15); }
    .header { background: linear-gradient(135deg, #ec4899 0%, #a855f7 50%, #f97316 100%); color: white; padding: 20px 24px; border-radius: 12px 12px 0 0; margin: -32px -32px 24px; }
    h1 { margin: 0; font-size: 24px; }
    .total { font-size: 36px; font-weight: 800; background: linear-gradient(135deg, #ec4899, #f97316); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .button { display: inline-block; background: linear-gradient(135deg, #ec4899, #a855f7, #f97316); color: white; padding: 14px 32px; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; box-shadow: 0 8px 24px -4px rgba(236, 72, 153, 0.4); }
    .info-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
    .info-table td { padding: 8px 0; border-bottom: 1px solid #fce7f3; font-size: 14px; }
    .info-table td:first-child { color: #6b7280; }
    .info-table td:last-child { text-align: right; font-weight: 600; }
    .footer { color: #888; font-size: 12px; margin-top: 16px; text-align: center; }
    .status-badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
    .status-open { background: #fef3c7; color: #92400e; }
    .status-past_due { background: #fee2e2; color: #991b1b; }
    .status-paid { background: #d1fae5; color: #065f46; }
</style>
</head>
<body>
<div class="card">
    <div class="header">
        <h1>{{ $invoice->number }}</h1>
        <div style="font-size:13px;opacity:0.9;margin-top:4px">HRIS Indonesia · SaaS Subscription</div>
    </div>

    <p>Halo Tim <strong>{{ $tenant->name }}</strong>,</p>

    @if ($invoice->status === 'open')
        <p>Berikut invoice subscription HRIS untuk periode <strong>{{ $invoice->period_start->format('d M') }} – {{ $invoice->period_end->format('d M Y') }}</strong>. Mohon lakukan pembayaran sebelum jatuh tempo.</p>
    @elseif ($invoice->status === 'past_due')
        <p>⚠️ Invoice ini sudah melewati jatuh tempo. Mohon segera lakukan pembayaran untuk menjaga subscription HRIS tetap aktif.</p>
    @elseif ($invoice->status === 'paid')
        <p>✅ Terima kasih! Pembayaran sudah kami terima. Receipt resmi sudah terlampir di sini.</p>
    @endif

    <div style="margin: 20px 0; padding: 16px; background: linear-gradient(135deg, #fdf2f8, #faf5ff); border-radius: 12px;">
        <div style="font-size:13px;color:#6b7280;font-weight:600">Total Tagihan</div>
        <div class="total">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</div>
        <div style="font-size:12px;color:#6b7280;margin-top:4px">incl. PPN 11%</div>
    </div>

    <table class="info-table">
        <tr><td>Status</td><td><span class="status-badge status-{{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span></td></tr>
        <tr><td>Issue Date</td><td>{{ $invoice->issue_date->format('d M Y') }}</td></tr>
        <tr><td>Due Date</td><td>{{ $invoice->due_date->format('d M Y') }}</td></tr>
        <tr><td>Periode</td><td>{{ $invoice->period_start->format('d M') }} – {{ $invoice->period_end->format('d M Y') }}</td></tr>
        @if ($invoice->status === 'paid')
            <tr><td>Paid at</td><td>{{ optional($invoice->paid_at)->format('d M Y H:i') }}</td></tr>
            <tr><td>Paid via</td><td>{{ $invoice->paid_via ?? '-' }}</td></tr>
        @endif
    </table>

    @if ($invoice->status !== 'paid')
        <div style="text-align:center;margin:24px 0">
            <a href="{{ $payUrl }}" class="button">💳 Bayar Sekarang →</a>
        </div>

        <p style="font-size:13px;color:#6b7280;text-align:center">
            Atau salin link berikut ke browser:<br>
            <code style="font-size:11px;background:#f9f9f9;padding:4px 8px;border-radius:4px">{{ $payUrl }}</code>
        </p>
    @endif

    <p style="font-size:13px;color:#6b7280;margin-top:24px">
        Pertanyaan? Reply email ini atau hubungi support kami via WhatsApp.<br>
        Terima kasih sudah memilih HRIS Indonesia! 💕
    </p>
</div>

<div class="footer">
    HRIS Indonesia · Made in 🇮🇩 · {{ config('app.url') }}<br>
    Email ini dikirim otomatis. Jangan reply jika tidak ada pertanyaan.
</div>
</body>
</html>
