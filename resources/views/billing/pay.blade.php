@extends('marketing.layouts.app')

@section('title', $title)
@section('description', $description)

@section('content')

<section class="relative bg-candy-soft min-h-screen py-12 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="blob blob-pink w-96 h-96 top-10 -left-20"></div>
        <div class="blob blob-purple w-96 h-96 bottom-10 -right-20"></div>
    </div>

    <div class="relative max-w-4xl mx-auto px-6">
        {{-- Invoice Summary Card --}}
        <div class="bg-white/90 backdrop-blur rounded-3xl shadow-candy-lg p-8 mb-6 ring-1 ring-candy-pink-100">
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <div class="text-xs font-bold text-candy-purple-600 uppercase tracking-wider mb-1">Invoice</div>
                    <h1 class="text-3xl font-extrabold text-gray-900">{{ $invoice->number }}</h1>
                    <div class="text-sm text-gray-600 mt-2">
                        🏢 {{ $invoice->tenant->name }}<br>
                        📅 Periode: {{ $invoice->period_start->format('d M') }} – {{ $invoice->period_end->format('d M Y') }}<br>
                        ⏰ Jatuh tempo: <strong class="text-red-600">{{ $invoice->due_date->format('d M Y') }}</strong>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-xs font-bold text-candy-pink-600 uppercase tracking-wider mb-1">Total Tagihan</div>
                    <div class="text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-candy-pink-500 to-brand-500">
                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">incl. PPN 11%</div>
                </div>
            </div>

            @if ($invoice->line_items)
                <div class="mt-6 pt-6 border-t border-candy-pink-100">
                    <div class="text-xs font-bold text-gray-700 mb-2">Rincian:</div>
                    @foreach ($invoice->line_items as $item)
                        <div class="flex justify-between text-sm py-1">
                            <div class="text-gray-700">{{ $item['description'] ?? '-' }}</div>
                            <div class="font-semibold text-gray-900">Rp {{ number_format($item['amount'] ?? 0, 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($invoice->status === 'paid')
                <div class="mt-6 bg-candy-mint-50 border border-candy-mint-200 rounded-2xl p-4 text-center">
                    <div class="text-2xl mb-1">✅</div>
                    <div class="font-bold text-candy-mint-700">Invoice Sudah Dibayar</div>
                    <div class="text-sm text-candy-mint-600">via {{ $invoice->paid_via ?? '-' }} pada {{ optional($invoice->paid_at)->format('d M Y H:i') }}</div>
                </div>
            @endif
        </div>

        @if ($invoice->status !== 'paid')
            {{-- Payment Method Picker --}}
            <div class="bg-white/90 backdrop-blur rounded-3xl shadow-candy p-8 ring-1 ring-candy-pink-100">
                <h2 class="text-2xl font-extrabold mb-2">Pilih Metode Pembayaran</h2>
                <p class="text-gray-600 mb-6">Pilih salah satu metode di bawah. Pembayaran diproses langsung setelah konfirmasi.</p>

                @if (empty($methods))
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center">
                        <div class="text-3xl mb-2">⚠️</div>
                        <div class="font-bold text-amber-900">Belum ada payment gateway dikonfigurasi</div>
                        <p class="text-sm text-amber-700 mt-1">Admin tenant perlu setup payment adapter di
                            <a href="/admin/adapter-configs" class="underline">Settings → Integrasi (BYOK)</a>
                            dengan domain "payment".
                        </p>
                    </div>
                @else
                    <form action="{{ route('billing.process', $token) }}" method="POST" class="space-y-6">
                        @csrf
                        @foreach ($methods as $bucketKey => $group)
                            <div>
                                <h3 class="font-bold text-gray-900 mb-3">{{ $group['title'] }}</h3>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($group['items'] as $adapter)
                                        <label class="cursor-pointer group">
                                            <input type="radio" name="adapter_config_id" value="{{ $adapter->id }}" class="peer sr-only"
                                                   data-method="{{ $bucketKey }}"
                                                   onchange="document.getElementById('payment_method').value='{{ $bucketKey }}'" required>
                                            <div class="p-4 rounded-2xl border-2 border-gray-200 bg-white hover:border-candy-pink-300
                                                        peer-checked:border-candy-pink-500 peer-checked:bg-candy-pink-50
                                                        peer-checked:shadow-candy peer-checked:scale-105
                                                        transition transform">
                                                <div class="text-3xl mb-2">{{ $group['icon'] }}</div>
                                                <div class="font-bold text-sm text-gray-900">{{ $adapter->name }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5">{{ $adapter->format }}</div>
                                                <div class="hidden peer-checked:block mt-2 text-xs text-candy-pink-600 font-bold">✓ Dipilih</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <input type="hidden" name="payment_method" id="payment_method" value="redirect">

                        <div class="pt-6 border-t border-candy-pink-100">
                            <label class="flex items-start gap-3 text-sm text-gray-700 mb-4">
                                <input type="checkbox" required class="mt-1">
                                <span>Saya setuju dengan <a href="/terms" class="text-candy-pink-600 underline">Terms of Service</a> + <a href="/privacy" class="text-candy-pink-600 underline">Privacy Policy</a> (UU PDP).</span>
                            </label>

                            <button type="submit" class="w-full bg-gradient-to-r from-candy-pink-500 via-candy-purple-500 to-brand-500 text-white py-4 rounded-2xl text-lg font-extrabold shadow-candy-lg hover:shadow-candy-glow transition transform hover:-translate-y-0.5">
                                💳 Bayar Rp {{ number_format($invoice->total_amount, 0, ',', '.') }} Sekarang →
                            </button>

                            <div class="mt-4 flex items-center justify-center gap-6 text-xs text-gray-500 flex-wrap">
                                <span>🔒 SSL Encrypted</span>
                                <span>✓ PCI DSS Compliant Gateway</span>
                                <span>🛡 UU PDP Protected</span>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        @endif

        <div class="mt-6 text-center text-sm text-gray-500">
            Bukan invoice Anda? <a href="/" class="text-candy-pink-600 hover:underline">Kembali ke HRIS Indonesia</a>
        </div>
    </div>
</section>

@endsection
