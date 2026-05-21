@extends('marketing.layouts.app')
@section('title', $title)

@section('content')
<section class="bg-candy-soft min-h-screen py-20">
    <div class="max-w-xl mx-auto px-6 text-center">
        <div class="bg-white/90 backdrop-blur rounded-3xl shadow-candy p-8">
            <div class="text-6xl mb-4">😢</div>
            <h1 class="text-3xl font-extrabold mb-2">Pembayaran Dibatalkan</h1>
            <p class="text-gray-600 mb-6">Invoice {{ $invoice->number }} belum dibayar. Anda bisa coba lagi kapan saja sebelum jatuh tempo {{ $invoice->due_date->format('d M Y') }}.</p>
            <a href="{{ route('billing.pay', \App\Http\Controllers\Web\BillingController::tokenFor($invoice)) }}"
               class="inline-block bg-gradient-to-r from-candy-pink-500 to-brand-500 text-white px-8 py-4 rounded-2xl font-bold shadow-candy">
                Coba Bayar Lagi
            </a>
        </div>
    </div>
</section>
@endsection
