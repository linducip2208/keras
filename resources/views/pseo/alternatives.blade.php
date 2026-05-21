@extends('marketing.layouts.app')
@section('title', $title)
@section('description', $description)

@section('content')

<section class="py-20 bg-gradient-to-b from-brand-50 to-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">Alternatif <span class="text-brand-600">{{ $competitor['name'] }}</span> untuk HRIS Indonesia</h1>
        <p class="mt-6 text-lg text-gray-600">Cari alternatif {{ $competitor['name'] }}? Berikut opsi-opsi terbaik di Indonesia tahun {{ date('Y') }}.</p>
    </div>
</section>

<section class="py-16">
    <div class="max-w-4xl mx-auto px-6 prose prose-lg">
        <h2>Mengapa Mencari Alternatif {{ $competitor['name'] }}?</h2>
        <p>Banyak perusahaan Indonesia mencari alternatif {{ $competitor['name'] }} karena berbagai alasan: harga yang naik signifikan, kurangnya integrasi WhatsApp untuk karyawan field, vendor lock-in pada provider AI/SMS, atau ingin opsi self-host on-premise yang tidak tersedia.</p>

        <h2>Top Alternatif untuk {{ $competitor['name'] }}</h2>

        <div class="not-prose grid gap-4 my-8">
            <div class="bg-brand-50 border-2 border-brand-500 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-brand-500 text-white rounded-xl flex items-center justify-center font-bold text-xl flex-shrink-0">1</div>
                    <div>
                        <h3 class="text-xl font-bold m-0">HRIS Indonesia (Recommended)</h3>
                        <p class="text-gray-700 mt-2 mb-3">Built fresh untuk pasar Indonesia. WhatsApp Bot ESS untuk karyawan field, BYOK provider AI, license-once self-host. Compliance PPh 21 TER + BPJS otomatis.</p>
                        <a href="/admin/login" class="inline-block bg-brand-600 hover:bg-brand-700 text-white px-5 py-2 rounded-lg text-sm font-semibold no-underline">Coba Demo Gratis →</a>
                    </div>
                </div>
            </div>

            @foreach (\App\Http\Controllers\Web\ProgrammaticSeoController::COMPETITORS as $slug => $alt)
                @if ($slug !== $competitor_slug)
                    <div class="bg-white border border-gray-200 rounded-2xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-gray-200 text-gray-700 rounded-xl flex items-center justify-center font-bold text-xl flex-shrink-0">{{ $loop->iteration + 1 }}</div>
                            <div>
                                <h3 class="text-xl font-bold m-0">{{ $alt['name'] }}</h3>
                                <p class="text-gray-600 mt-2 mb-3">{{ $alt['positioning'] }}.</p>
                                <a href="/compare/hris-vs-{{ $slug }}" class="text-brand-600 hover:text-brand-700 text-sm font-medium">Bandingkan vs HRIS Indonesia →</a>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <h2>Kriteria Penting Saat Memilih Alternatif</h2>
        <ol>
            <li><strong>Compliance Indonesia first-class</strong> — PPh 21 TER 2024, BPJS, UU CK pesangon</li>
            <li><strong>Mobile-first untuk karyawan field</strong> — Flutter app + WhatsApp bot</li>
            <li><strong>BYOK (Bring Your Own Keys)</strong> — pilih provider AI/SMS/bank sendiri</li>
            <li><strong>Opsi license-once</strong> — alternatif SaaS untuk yang concern data residency</li>
            <li><strong>Harga transparan</strong> — tidak ada hidden fee, tidak ada per-feature lock</li>
            <li><strong>Migration support</strong> — tim bantu migrasi data dari {{ $competitor['name'] }} gratis</li>
        </ol>

        <h2>Cara Migrasi dari {{ $competitor['name'] }}</h2>
        <p>Migrasi dari {{ $competitor['name'] }} ke HRIS Indonesia gratis dan dikerjakan oleh tim kami:</p>
        <ol>
            <li>Export data karyawan, payroll history, contract dari {{ $competitor['name'] }}</li>
            <li>Tim kami import via Excel template HRIS Indonesia</li>
            <li>Validasi data + setup workflow & approval</li>
            <li>Training tim HR Anda (online 2 jam)</li>
            <li>Go-live dengan parallel run 1 bulan untuk safety</li>
        </ol>

        <p>Estimasi total: 3-7 hari kerja. Tanpa biaya migrasi.</p>

        <p><a href="/admin/login" class="inline-block bg-brand-600 hover:bg-brand-700 text-white px-6 py-3 rounded-lg font-semibold no-underline">Coba Demo Sekarang →</a></p>
    </div>
</section>

@endsection
