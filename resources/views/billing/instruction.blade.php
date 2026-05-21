@extends('marketing.layouts.app')

@section('title', $title)

@section('content')

<section class="relative bg-candy-soft min-h-screen py-12 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="blob blob-mint w-96 h-96 top-10 -right-20"></div>
        <div class="blob blob-yellow w-72 h-72 bottom-20 -left-10"></div>
    </div>

    <div class="relative max-w-2xl mx-auto px-6">
        <div class="bg-white/90 backdrop-blur rounded-3xl shadow-candy-lg p-8 ring-1 ring-candy-mint-200">
            <div class="text-center mb-6">
                <div class="inline-block bg-gradient-to-br from-candy-mint-400 to-candy-blue-400 text-white px-4 py-2 rounded-full text-sm font-bold mb-3">
                    ⏳ Menunggu Pembayaran
                </div>
                <h1 class="text-3xl font-extrabold">Instruksi Pembayaran</h1>
                <p class="text-gray-600 mt-2">Invoice {{ $invoice->number }} · Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
            </div>

            @if ($payment->qr_string)
                <div class="bg-candy-pink-50 rounded-2xl p-6 text-center mb-4">
                    <div class="text-xs font-bold text-candy-pink-600 uppercase tracking-wider mb-3">QRIS Dynamic — Scan dengan E-Wallet Anda</div>
                    <div class="inline-block bg-white p-4 rounded-2xl shadow-candy">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode($payment->qr_string) }}" alt="QR Code" class="block">
                    </div>
                    <div class="mt-4 text-sm text-gray-600">
                        Scan QR code ini dengan: <strong>DANA, OVO, GoPay, ShopeePay, LinkAja, atau bank apapun</strong>
                    </div>
                </div>
            @endif

            @if ($payment->va_number)
                <div class="bg-candy-blue-50 rounded-2xl p-6 mb-4">
                    <div class="text-xs font-bold text-candy-blue-600 uppercase tracking-wider mb-2">Virtual Account · {{ strtoupper($payment->payment_channel ?? '') }}</div>
                    <div class="bg-white rounded-xl p-4 flex items-center justify-between gap-4">
                        <code class="text-2xl font-bold text-gray-900 tracking-wider">{{ $payment->va_number }}</code>
                        <button onclick="navigator.clipboard.writeText('{{ $payment->va_number }}')" class="bg-candy-blue-500 hover:bg-candy-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold">Salin</button>
                    </div>
                    <div class="mt-4 text-sm text-gray-700 space-y-1">
                        <div>• Transfer dari ATM / iBanking / MBanking ke nomor VA di atas</div>
                        <div>• Jumlah <strong>tepat Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</strong></div>
                        <div>• Pembayaran terdeteksi otomatis dalam 1-3 menit</div>
                    </div>
                </div>
            @endif

            @if ($payment->expires_at)
                <div class="bg-amber-50 rounded-2xl p-4 text-center text-sm text-amber-700">
                    ⏰ Berlaku sampai: <strong>{{ \Carbon\Carbon::parse($payment->expires_at)->format('d M Y H:i') }}</strong>
                </div>
            @endif

            <div class="mt-6 flex gap-3">
                <a href="{{ route('billing.pay', $invoice ? \App\Http\Controllers\Web\BillingController::tokenFor($invoice) : '/') }}"
                   class="flex-1 text-center bg-white border-2 border-gray-200 hover:border-gray-300 px-4 py-3 rounded-xl font-bold">
                    Ganti Metode
                </a>
                <button onclick="location.reload()" class="flex-1 bg-gradient-to-r from-candy-mint-500 to-candy-blue-500 text-white px-4 py-3 rounded-xl font-bold">
                    🔄 Refresh Status
                </button>
            </div>
        </div>
    </div>
</section>

@endsection
