@extends('marketing.layouts.app')
@section('title', $title)
@section('description', $description)

@push('jsonld')
<script type="application/ld+json">
@php
$cn = $competitor['name'];
echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => [
        ['@type'=>'Question','name'=>"Apa perbedaan HRIS Indonesia vs {$cn}?",'acceptedAnswer'=>['@type'=>'Answer','text'=>"HRIS Indonesia menawarkan WhatsApp Bot ESS, BYOK provider AI/SMS/Bank, dan license-once self-host yang tidak dimiliki {$cn}."]],
        ['@type'=>'Question','name'=>"Apakah lebih murah dari {$cn}?",'acceptedAnswer'=>['@type'=>'Answer','text'=>"Ya, HRIS Indonesia rata-rata 40% lebih murah untuk tier setara, plus opsi license-once tanpa per-user fee bulanan."]],
    ],
], JSON_UNESCAPED_SLASHES);
@endphp
</script>
@endpush

@section('content')

<section class="py-20 bg-gradient-to-b from-brand-50 to-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <div class="inline-flex items-center gap-3 text-2xl font-bold mb-4">
            <span class="px-4 py-2 bg-brand-500 text-white rounded-lg">HRIS Indonesia</span>
            <span class="text-gray-400">vs</span>
            <span class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg">{{ $competitor['name'] }}</span>
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold leading-tight mt-6">{{ $title }}</h1>
        <p class="mt-6 text-lg text-gray-600">Perbandingan head-to-head: fitur, harga, integrasi, dan kapan harus pilih yang mana.</p>
    </div>
</section>

<section class="py-16">
    <div class="max-w-5xl mx-auto px-6">
        <div class="overflow-x-auto rounded-2xl border border-gray-200">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Fitur</th>
                        <th class="px-4 py-3 text-center bg-brand-50">HRIS Indonesia</th>
                        <th class="px-4 py-3 text-center">{{ $competitor['name'] }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach([
                        ['PPh 21 TER 2024 (PMK 168/2023)', true, true],
                        ['BPJS Kesehatan + Ketenagakerjaan (5 program)', true, true],
                        ['Attendance GPS + Selfie', true, true],
                        ['Mock GPS Detection', true, false],
                        ['Offline Mode Attendance', true, false],
                        ['Face Recognition + Anti-spoofing', true, false],
                        ['WhatsApp Bot ESS Full-fitur', true, false],
                        ['AI CV Screening', true, true],
                        ['AI Narrative Insight', true, false],
                        ['Predictive Turnover Risk', true, false],
                        ['BYOK Provider AI (OpenAI, DeepSeek, Ollama)', true, false],
                        ['BYOK SMS Gateway', true, false],
                        ['BYOK Bank Disbursement (5+ bank)', true, true],
                        ['Multi-Company / Group', true, true],
                        ['OKR & 360° Performance', true, true],
                        ['9-box Matrix', true, true],
                        ['LMS Internal', true, false],
                        ['Letter Generator + E-sign', true, true],
                        ['Direct submit DJP Coretax API', true, false],
                        ['Direct submit BPJS SIPP API', true, false],
                        ['License-once Self-host', true, false],
                        ['Free Trial 14 hari', true, true],
                        ['Server Indonesia (UU PDP)', true, true],
                        ['Harga per user', 'Rp 10-25k', 'Rp 25-50k'],
                    ] as [$feature, $us, $them])
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium">{{ $feature }}</td>
                            <td class="px-4 py-3 text-center bg-brand-50/30">
                                @if(is_bool($us))
                                    {!! $us ? '<span class="text-green-600 font-bold">✓</span>' : '<span class="text-gray-300">—</span>' !!}
                                @else
                                    <span class="font-semibold text-brand-600">{{ $us }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(is_bool($them))
                                    {!! $them ? '<span class="text-green-600 font-bold">✓</span>' : '<span class="text-red-400">✗</span>' !!}
                                @else
                                    <span>{{ $them }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="prose prose-lg max-w-none mt-12">
            <h2>Kapan Pilih HRIS Indonesia?</h2>
            <ul>
                <li>Karyawan banyak yang field/blue-collar — <strong>WhatsApp Bot ESS</strong> game-changer</li>
                <li>Anda mau kontrol biaya AI/SMS — pilih provider sendiri (BYOK)</li>
                <li>Concern data residency — opsi license-once self-host di server Anda</li>
                <li>Budget sensitive — rata-rata 40% lebih murah dari {{ $competitor['name'] }}</li>
                <li>Butuh AI insight + predictive analytics built-in (bukan add-on terpisah)</li>
            </ul>

            <h2>Kapan Pilih {{ $competitor['name'] }}?</h2>
            <ul>
                <li>Sudah pakai ekosistem mereka (e.g., Mekari Jurnal + KlikPajak untuk Talenta)</li>
                <li>Brand established lebih penting dari fitur baru</li>
                <li>Tim Anda sudah familiar dengan UI-nya, tidak mau training ulang</li>
            </ul>

            <h2>Migrasi dari {{ $competitor['name'] }} ke HRIS Indonesia</h2>
            <p>Tim kami menyediakan layanan migrasi gratis: data karyawan, payroll history, contract, attendance log — semua diimpor otomatis. Estimasi 3-7 hari kerja tergantung volume data. Coba demo dulu, baru putuskan.</p>

            <p><a href="/admin/login" class="inline-block bg-brand-600 hover:bg-brand-700 text-white px-6 py-3 rounded-lg font-semibold no-underline">Coba Demo Sekarang →</a></p>
        </div>
    </div>
</section>

@endsection
