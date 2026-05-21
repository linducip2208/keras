@extends('marketing.layouts.app')

@section('title', 'HRIS — Sistem Manajemen SDM Indonesia All-in-One')
@section('description', 'HRIS Indonesia satu-satunya dengan WhatsApp Bot ESS, BYOK provider AI, dan license-once self-host. Payroll PPh 21 TER 2024 + BPJS otomatis. Mulai gratis 14 hari.')

@push('jsonld')
<script type="application/ld+json">@verbatim
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "HRIS Indonesia",
  "applicationCategory": "BusinessApplication",
  "operatingSystem": "Web, iOS, Android",
  "offers": [
    {"@type":"Offer","name":"Starter","price":"10000","priceCurrency":"IDR"},
    {"@type":"Offer","name":"Basic","price":"15000","priceCurrency":"IDR"},
    {"@type":"Offer","name":"Essential","price":"25000","priceCurrency":"IDR"}
  ]
}
@endverbatim</script>
@endpush

@php
$industries = [
    ['🏭', 'Manufaktur', 'Shift rotasi + overtime'],
    ['🍽️', 'F&B Retail', 'Multi-outlet + part-time'],
    ['🏗️', 'Konstruksi', 'Site-based + harian'],
    ['💼', 'Startup Tech', 'OKR + 360 review'],
    ['🏥', 'Healthcare', 'Shift 24-7 + on-call'],
];
$beforeAfter = [
    ['Payroll lembur 3 hari akhir bulan', 'Payroll selesai 30 menit, PPh & BPJS otomatis'],
    ['Cuti via WA chat — sering miss', 'Cuti via app, approver swipe, notif otomatis'],
    ['Slip gaji kirim 1-1 via WA password manual', 'Slip terkirim otomatis ke WA, password locked'],
    ['Lupa update PPh 21 TER → SP DJP', 'Aturan pajak update otomatis, audit-ready'],
    ['Owner kunci di Talenta, susah switch', 'BYOK, ganti provider AI/SMS/bank kapan saja'],
];
$features = [
    ['💰', 'Payroll PPh 21 TER 2024', 'Hitung gaji otomatis sesuai PMK 168/2023. Metode Gross / Nett / Gross-up.', ['Multi-period (bulanan/mingguan)','PPh 21 final pesangon','Form 1721-A1 & 1721-I','Queue 5 menit untuk 500 karyawan']],
    ['🏥', 'BPJS 5 Program Lengkap', 'Kesehatan + JHT + JP + JKK + JKM dengan cap upah otomatis. File SIPP siap upload.', ['Cap 12jt Kesehatan, 10.042jt JP 2026','Dependents suami/istri + 3 anak','Iuran per UMK per cabang','Klaim JKK/JKM workflow']],
    ['📍', 'Attendance Anti-Cheat', 'GPS + selfie + face recognition. Mock GPS detection. Offline mode.', ['Geofence per cabang','Live tracking dashboard','Fingerprint device SDK','WhatsApp clock-in']],
    ['💬', 'WhatsApp Bot ESS', 'Clock-in, cuti, slip gaji, approve — semua dari WA. No app install.', ['Adapter dynamic (Meta, Wati, Fonnte)','Reply OK untuk approve','Slip password-locked via WA','Broadcast ack tracking']],
    ['🎯', 'OKR & 360° Performance', 'Cascading goal company → individu. 9-box matrix. Calibration meeting.', ['Weekly check-in','Anonymous peer review','Goal-linked appraisal','AI summary feedback']],
    ['🧠', 'BYOK Provider AI', 'Bring Your Own Keys. OpenAI, DeepSeek, Groq, Ollama — semua kompatibel.', ['CV screening AI','OCR receipt reimbursement','Narrative insight analytics','Tracking cost transparan']],
    ['📄', 'Letter Generator + E-sign', 'SK, paklaring, SP, mutasi — sekali klik PDF + e-sign BSrE/Privy.', ['Template variable merge','Nomor surat auto-increment','Bulk generate 200 SK','Arsip otomatis']],
    ['🏢', 'Multi-Company Group', 'Satu admin → banyak PT. Konsolidasi headcount lintas entity.', ['Multi-cabang per company','Cross-company transfer','Group dashboard executive','Per-entity payroll terpisah']],
    ['🔐', 'License-Once Self-host', 'Beli sekali pakai selamanya. Source code dilindungi license v3 dari whitelabel.co.id.', ['On-premise di server sendiri','Data residency Indonesia','Tanpa per-user fee bulanan','Annual maintenance opsional']],
];
$demoAccounts = [
    ['Owner / Super Admin', 'owner@hris.test', 'password', 'Full akses semua modul'],
    ['HR Manager', 'hr@hris.test', 'password', 'Employee, payroll, attendance, leave'],
    ['HR Staff', 'hrstaff@hris.test', 'password', 'Limited HR (no payroll approve)'],
    ['Finance Manager', 'finance@hris.test', 'password', 'Payroll approve, disbursement'],
    ['Manager Sales', 'manager-sales@hris.test', 'password', 'Approve cuti/OT tim sales'],
    ['Manager Tech', 'manager-tech@hris.test', 'password', 'Approve cuti/OT tim engineer'],
    ['Karyawan Office', 'employee@hris.test', 'password', 'ESS personal scope'],
    ['Karyawan Field', 'field@hris.test', 'password', 'Mobile + WA bot'],
];
$pricing = [
    ['Starter', 'Rp 10.000', '/karyawan/bulan', 'Untuk startup < 25 karyawan', ['Employee, Attendance, Payroll, BPJS, Leave', 'Mobile app (iOS+Android)', 'Email support', 'Trial 14 hari'], false],
    ['Basic', 'Rp 15.000', '/karyawan/bulan', 'UKM 26-100 karyawan', ['Semua Starter +', 'Reimburse + Letter generator', 'Bank disburse (5 bank)', 'WhatsApp bot ESS', 'Chat support'], true],
    ['Essential', 'Rp 25.000', '/karyawan/bulan', 'Mid-market 101-500 karyawan', ['Semua Basic +', 'Recruitment + OKR + Appraisal', 'LMS + Asset + Travel + Loan', 'Analytics + AI insight', 'Dedicated AM'], false],
    ['License-Once', 'Rp 75 jt+', 'sekali bayar', 'Self-host on-premise', ['Source code dilindungi (v3)', 'Server Anda sendiri', 'Tanpa per-user fee', 'Annual maintenance 20%', 'Migrasi data ditanggung'], false],
];
@endphp

@section('content')

{{-- HERO --}}
<section class="relative bg-gradient-to-b from-brand-50 via-white to-white overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_30%_20%,rgba(249,115,22,0.15),transparent_50%)]"></div>
    <div class="max-w-7xl mx-auto px-6 pt-20 pb-24 text-center">
        <div class="inline-flex items-center gap-2 bg-brand-100 text-brand-800 px-4 py-1.5 rounded-full text-sm font-medium mb-6">
            🇮🇩 PPh 21 TER 2024 + BPJS Auto-Update · UU PDP Compliant
        </div>
        <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight max-w-4xl mx-auto leading-tight">
            HRIS yang <span class="text-brand-600">Karyawanmu Pakai dari WhatsApp</span>.<br class="hidden md:block">
            Owner Pilih Provider AI Sendiri.
        </h1>
        <p class="mt-6 text-lg md:text-xl text-gray-600 max-w-3xl mx-auto">
            Payroll 5 menit. PPh 21 TER + BPJS otomatis. Clock-in via GPS+selfie atau WA. <strong>BYOK</strong> AI, SMS, bank — kamu kontrol sendiri. License-once self-host tersedia.
        </p>
        <div class="mt-10 flex items-center justify-center gap-4 flex-wrap">
            <a href="/admin/login" class="bg-brand-600 hover:bg-brand-700 text-white px-8 py-4 rounded-xl text-base font-semibold shadow-lg shadow-brand-500/30">Coba Demo Gratis 14 Hari →</a>
            <a href="#demo" class="border border-gray-300 hover:border-gray-400 px-8 py-4 rounded-xl text-base font-semibold">Lihat Demo Akun</a>
        </div>
        <div class="mt-12 flex items-center justify-center gap-8 text-xs text-gray-500 flex-wrap">
            <div>✓ Tanpa kartu kredit</div>
            <div>✓ Migrasi data dari Talenta/Gadjian dibantu</div>
            <div>✓ Server di Indonesia (UU PDP)</div>
        </div>
    </div>
</section>

{{-- TRUST STRIP --}}
<section class="border-y border-gray-200 bg-white">
    <div class="max-w-7xl mx-auto px-6 py-12">
        <p class="text-center text-sm font-medium text-gray-500 mb-8">Cocok untuk berbagai industri:</p>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6 text-center">
            @foreach ($industries as $ind)
                <div>
                    <div class="text-4xl mb-2">{{ $ind[0] }}</div>
                    <div class="font-semibold">{{ $ind[1] }}</div>
                    <div class="text-xs text-gray-500">{{ $ind[2] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- PROBLEM-SOLUTION --}}
<section class="bg-gray-50 py-20">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-14">
            <h2 class="text-4xl font-bold">Dari Excel-Kacau ke <span class="text-brand-600">Single Source of Truth</span></h2>
        </div>
        <div class="grid md:grid-cols-2 gap-6 max-w-5xl mx-auto">
            @foreach ($beforeAfter as $ba)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs font-bold text-red-600 mb-2">SEBELUM</div>
                            <p class="text-sm text-gray-600">{{ $ba[0] }}</p>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-green-600 mb-2">SESUDAH</div>
                            <p class="text-sm text-gray-900 font-medium">{{ $ba[1] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FEATURES --}}
<section id="features" class="py-24">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold tracking-tight">Semua yang Kamu Butuhkan, dalam Satu Sistem</h2>
            <p class="mt-4 text-lg text-gray-600">29 modul, 300+ fitur. Compliance Indonesia first-class.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @foreach ($features as $f)
                <div class="bg-white rounded-2xl p-6 border border-gray-200 hover:border-brand-300 hover:shadow-lg transition group">
                    <div class="text-4xl mb-4">{{ $f[0] }}</div>
                    <h3 class="text-xl font-bold mb-2 group-hover:text-brand-600 transition">{{ $f[1] }}</h3>
                    <p class="text-gray-600 text-sm mb-4">{{ $f[2] }}</p>
                    <ul class="space-y-2 text-sm">
                        @foreach ($f[3] as $b)
                            <li class="flex items-start gap-2">
                                <span class="text-green-500 mt-0.5">✓</span>
                                <span class="text-gray-700">{{ $b }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- DEMO ACCOUNTS --}}
<section id="demo" class="bg-gradient-to-br from-gray-900 to-gray-950 text-white py-24">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold">Coba Akun Demo Sekarang</h2>
            <p class="mt-3 text-gray-400">8 role demo siap pakai. Login langsung tanpa daftar.</p>
        </div>
        <div class="overflow-x-auto rounded-2xl border border-gray-700">
            <table class="w-full text-sm">
                <thead class="bg-gray-800 text-left">
                    <tr>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Password</th>
                        <th class="px-4 py-3">Cakupan</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @foreach ($demoAccounts as $acc)
                        <tr class="hover:bg-gray-800/50">
                            <td class="px-4 py-3 font-medium">{{ $acc[0] }}</td>
                            <td class="px-4 py-3 font-mono text-brand-300">{{ $acc[1] }}</td>
                            <td class="px-4 py-3 font-mono text-gray-400">{{ $acc[2] }}</td>
                            <td class="px-4 py-3 text-gray-300">{{ $acc[3] }}</td>
                            <td class="px-4 py-3"><a href="/admin/login" class="text-brand-400 hover:text-brand-300">Login →</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- PRICING --}}
<section id="pricing" class="py-24">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-14">
            <h2 class="text-4xl md:text-5xl font-bold tracking-tight">Harga Transparan, Tanpa Lock-in</h2>
            <p class="mt-4 text-lg text-gray-600">Per karyawan per bulan. Atau license-once untuk self-host.</p>
        </div>
        <div class="grid md:grid-cols-4 gap-6">
            @foreach ($pricing as $p)
                @php $highlighted = $p[5]; @endphp
                <div class="relative rounded-2xl p-6 border {{ $highlighted ? 'border-brand-500 bg-brand-50 shadow-xl scale-105' : 'border-gray-200 bg-white' }}">
                    @if ($highlighted)
                        <div class="absolute -top-3 right-6 bg-brand-600 text-white text-xs px-3 py-1 rounded-full font-semibold">RECOMMENDED</div>
                    @endif
                    <div class="font-bold text-lg">{{ $p[0] }}</div>
                    <div class="mt-4">
                        <span class="text-3xl font-extrabold">{{ $p[1] }}</span>
                        <span class="text-sm text-gray-600">{{ $p[2] }}</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-2 mb-5">{{ $p[3] }}</p>
                    <ul class="space-y-2 text-sm mb-6">
                        @foreach ($p[4] as $feat)
                            <li class="flex items-start gap-2">
                                <span class="text-green-500 mt-0.5">✓</span>
                                <span>{{ $feat }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <a href="/admin/login" class="block w-full text-center py-2.5 rounded-lg font-semibold {{ $highlighted ? 'bg-brand-600 hover:bg-brand-700 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-900' }}">Pilih {{ $p[0] }}</a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FINAL CTA --}}
<section class="bg-gradient-to-br from-brand-600 to-brand-800 text-white py-20">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h2 class="text-4xl md:text-5xl font-bold mb-4">Siap Stop Lembur Tiap Akhir Bulan?</h2>
        <p class="text-lg text-brand-100 mb-8">Coba demo 14 hari gratis. Tanpa kartu kredit. Migrasi data ditanggung tim kami.</p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            <a href="/admin/login" class="bg-white text-brand-700 hover:bg-brand-50 px-8 py-4 rounded-xl font-bold shadow-xl">Mulai Demo Gratis</a>
            <a href="https://wa.me/6281234567890" class="border-2 border-white/30 hover:bg-white/10 px-8 py-4 rounded-xl font-bold">Konsultasi via WA</a>
        </div>
    </div>
</section>

@endsection
