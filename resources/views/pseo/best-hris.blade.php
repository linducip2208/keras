@extends('marketing.layouts.app')

@section('title', $title)
@section('description', $description)

@push('jsonld')
<script type="application/ld+json">
@php
$jsonld = [
    '@context' => 'https://schema.org',
    '@type' => 'ItemList',
    'name' => $title,
    'numberOfItems' => count($competitors) + 1,
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'item' => ['@type' => 'SoftwareApplication', 'name' => 'HRIS Indonesia']],
    ],
];
$pos = 2;
foreach ($competitors as $slug => $c) {
    $jsonld['itemListElement'][] = ['@type' => 'ListItem', 'position' => $pos++, 'item' => ['@type' => 'SoftwareApplication', 'name' => $c['name']]];
}
echo json_encode($jsonld, JSON_UNESCAPED_SLASHES);
@endphp
</script>
@endpush

@section('content')

<section class="py-20 bg-gradient-to-b from-brand-50 to-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <div class="inline-block bg-brand-100 text-brand-800 px-4 py-1.5 rounded-full text-sm font-medium mb-6">
            🏆 Update {{ $year }}
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">{{ $title }}</h1>
        <p class="mt-6 text-lg text-gray-600">Riset & perbandingan menyeluruh 8 software HRIS terbaik di Indonesia tahun {{ $year }}. Fitur, harga, dan differentiator unik tiap produk.</p>
    </div>
</section>

<section class="py-16">
    <div class="max-w-5xl mx-auto px-6">
        <h2 class="text-3xl font-bold mb-8">Daftar 8 Aplikasi HRIS Terbaik {{ $year }}</h2>

        <div class="space-y-6">
            {{-- Our product first --}}
            <div class="bg-gradient-to-r from-brand-50 to-white border-2 border-brand-500 rounded-2xl p-6 shadow-lg">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 bg-brand-500 text-white rounded-xl flex items-center justify-center font-bold text-xl flex-shrink-0">1</div>
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold">HRIS Indonesia <span class="text-sm font-normal text-brand-600 ml-2">⭐ EDITOR'S CHOICE</span></h3>
                        <p class="mt-2 text-gray-700">Satu-satunya HRIS Indonesia yang menawarkan <strong>WhatsApp Bot ESS</strong>, <strong>BYOK provider AI</strong>, dan <strong>license-once self-host</strong>. Cocok untuk UKM hingga enterprise. Compliance PPh 21 TER + BPJS otomatis update.</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">PPh 21 TER 2024</span>
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">BPJS 5 program</span>
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">WhatsApp Bot</span>
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">License-once tersedia</span>
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">BYOK AI/SMS/Bank</span>
                        </div>
                        <div class="mt-4 text-sm text-gray-600">
                            <strong>Harga:</strong> Rp 10k - 25k / karyawan / bulan, atau license-once mulai Rp 75jt.
                        </div>
                        <a href="/admin/login" class="inline-block mt-4 bg-brand-600 hover:bg-brand-700 text-white px-5 py-2 rounded-lg text-sm font-semibold">Coba Demo Gratis →</a>
                    </div>
                </div>
            </div>

            @foreach ($competitors as $slug => $c)
                <div class="bg-white border border-gray-200 rounded-2xl p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gray-200 text-gray-700 rounded-xl flex items-center justify-center font-bold text-xl flex-shrink-0">{{ $loop->iteration + 1 }}</div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold">{{ $c['name'] }}</h3>
                            <p class="mt-2 text-gray-700">{{ $c['positioning'] }}. Salah satu HRIS yang populer di Indonesia. Cocok untuk perusahaan yang sudah punya alur kerja established dan tidak butuh fleksibilitas BYOK.</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Payroll PPh 21</span>
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">BPJS</span>
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">Attendance</span>
                            </div>
                            <div class="mt-4 flex gap-3">
                                <a href="/compare/hris-vs-{{ $slug }}" class="text-brand-600 hover:text-brand-700 text-sm font-medium">Bandingkan vs HRIS →</a>
                                <a href="/alternatives-to-{{ $slug }}" class="text-brand-600 hover:text-brand-700 text-sm font-medium">Lihat alternatif</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-16 prose prose-lg max-w-none">
            <h2>Bagaimana Cara Memilih HRIS Terbaik untuk Perusahaan Anda?</h2>
            <p>Memilih HRIS yang tepat bergantung pada ukuran perusahaan, industri, kompleksitas shift, dan budget. Berikut framework keputusan yang kami rekomendasikan:</p>

            <h3>1. Compliance Indonesia First</h3>
            <p>HRIS yang baik harus <strong>auto-update PPh 21 TER 2024</strong> (PMK 168/2023), <strong>BPJS dengan cap upah terkini</strong> (2026: cap JP Rp 10.042.300, Kesehatan Rp 12 juta), dan support <strong>UU Cipta Kerja</strong> untuk pesangon. Tanpa ini, perusahaanmu beresiko denda dari DJP atau BPJSTK.</p>

            <h3>2. Mobile + WhatsApp untuk Karyawan</h3>
            <p>Karyawan field, blue-collar, atau yang gak punya laptop butuh akses ESS via HP. <strong>WhatsApp Bot ESS</strong> adalah game-changer — clock-in, lihat slip, cuti — semua dari WA tanpa install app. Hanya kami yang punya fitur ini di Indonesia.</p>

            <h3>3. BYOK (Bring Your Own Keys)</h3>
            <p>Jangan terkunci ke vendor AI/SMS/Bank tertentu. Pilih HRIS yang membiarkan Anda input API key sendiri (OpenAI, DeepSeek, Groq, Ollama self-host, dll.). Anda kontrol biaya AI, switching kapan saja tanpa vendor lock-in.</p>

            <h3>4. SaaS atau License-Once</h3>
            <p>Untuk perusahaan yang concern dengan data residency, ada opsi license-once self-host. Bayar sekali, deploy di server sendiri di Indonesia, source code dilindungi license v3 dari whitelabel.co.id. Tidak ada per-user fee bulanan yang menumpuk.</p>

            <h3>5. Multi-Company / Group</h3>
            <p>Holding company dengan banyak entitas butuh satu admin untuk semua. HRIS yang baik harus support multi-company dengan konsolidasi reporting tapi separate payroll/BPJS/NPWP per entity.</p>

            <h2>Ringkasan</h2>
            <p>Di {{ $year }}, pasar HRIS Indonesia makin matang. <strong>HRIS Indonesia (kami)</strong> hadir sebagai opsi modern dengan BYOK, WhatsApp Bot, dan license-once — fitur yang tidak ada di competitor. Bandingkan langsung dengan <a href="/compare/hris-vs-talenta">Mekari Talenta</a> atau <a href="/compare/hris-vs-gadjian">Gadjian</a>, atau coba demo akun kami sekarang.</p>
        </div>
    </div>
</section>

@endsection
