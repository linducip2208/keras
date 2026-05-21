@extends('marketing.layouts.app')

@section('title', $title)

@section('content')

<section class="relative bg-gradient-to-br from-candy-mint-50 via-candy-blue-50 to-candy-purple-50 min-h-screen py-20 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="blob blob-mint w-96 h-96 top-10 -left-20 animate-float"></div>
        <div class="blob blob-purple w-96 h-96 bottom-10 -right-20 animate-float" style="animation-delay: 2s"></div>
    </div>

    <div class="relative max-w-2xl mx-auto px-6 text-center">
        <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-candy-mint-400 to-candy-mint-500 rounded-full mb-6 shadow-candy-glow">
            <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path d="M16.7 5.3a1 1 0 010 1.4l-7 7a1 1 0 01-1.4 0l-3-3a1 1 0 011.4-1.4l2.3 2.3 6.3-6.3a1 1 0 011.4 0z"/>
            </svg>
        </div>
        <h1 class="text-5xl font-extrabold mb-4">Pembayaran Berhasil! 🎉</h1>
        <p class="text-xl text-gray-700 mb-8">
            Invoice <strong>{{ $invoice->number }}</strong> sudah lunas. Subscription Anda diperpanjang otomatis.
        </p>

        <div class="bg-white/90 backdrop-blur rounded-3xl shadow-candy-lg p-6 text-left mb-8">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-xs font-bold text-candy-purple-600 uppercase tracking-wider">Invoice</div>
                    <div class="font-bold text-gray-900">{{ $invoice->number }}</div>
                </div>
                <div>
                    <div class="text-xs font-bold text-candy-purple-600 uppercase tracking-wider">Total</div>
                    <div class="font-bold text-gray-900">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</div>
                </div>
                <div>
                    <div class="text-xs font-bold text-candy-purple-600 uppercase tracking-wider">Periode</div>
                    <div class="font-bold text-gray-900">{{ $invoice->period_start->format('d M') }} – {{ $invoice->period_end->format('d M Y') }}</div>
                </div>
                <div>
                    <div class="text-xs font-bold text-candy-purple-600 uppercase tracking-wider">Status</div>
                    <div class="font-bold text-candy-mint-600">✓ PAID</div>
                </div>
            </div>
        </div>

        <div class="flex gap-4 justify-center">
            <a href="/admin/login" class="bg-gradient-to-r from-candy-pink-500 via-candy-purple-500 to-brand-500 text-white px-8 py-4 rounded-2xl font-bold shadow-candy">
                Login Portal HRIS →
            </a>
            <a href="/" class="bg-white border-2 border-gray-200 px-8 py-4 rounded-2xl font-bold">
                Kembali ke Beranda
            </a>
        </div>

        <div class="mt-8 text-sm text-gray-500">
            Receipt + invoice PDF akan dikirim ke email billing tenant dalam 5 menit.
        </div>
    </div>
</section>

@endsection
