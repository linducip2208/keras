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

{{-- HERO — Candy Hero --}}
<section class="relative bg-candy-hero overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="blob blob-pink w-96 h-96 top-10 -left-20 animate-float"></div>
        <div class="blob blob-purple w-80 h-80 top-32 right-10 animate-float" style="animation-delay: 2s"></div>
        <div class="blob blob-yellow w-72 h-72 bottom-10 left-1/4 animate-float" style="animation-delay: 4s"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-6 pt-20 pb-28 text-center">
        <div class="inline-flex items-center gap-2 bg-white/80 backdrop-blur text-candy-purple-700 px-5 py-2 rounded-full text-sm font-semibold mb-6 shadow-candy">
            ✨ PPh 21 TER 2024 + BPJS Auto-Update · UU PDP Compliant
        </div>
        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight max-w-5xl mx-auto leading-[1.05]">
            HRIS yang <span class="bg-clip-text text-transparent bg-gradient-to-r from-candy-pink-500 via-candy-purple-500 to-brand-500">Karyawanmu Pakai dari WhatsApp</span>.<br class="hidden md:block">
            <span class="text-gray-900">Owner Pilih Provider AI Sendiri.</span>
        </h1>
        <p class="mt-8 text-lg md:text-xl text-gray-700 max-w-3xl mx-auto leading-relaxed">
            Payroll 5 menit. PPh 21 TER + BPJS otomatis. Clock-in via GPS+selfie atau WA.
            <strong class="text-candy-pink-600">BYOK</strong> AI, SMS, bank — kamu kontrol sendiri.
            License-once self-host tersedia.
        </p>
        <div class="mt-10 flex items-center justify-center gap-4 flex-wrap">
            <a href="/admin/login" class="bg-gradient-to-r from-candy-pink-500 via-candy-purple-500 to-brand-500 text-white px-8 py-4 rounded-2xl text-base font-bold shadow-candy-lg hover:shadow-candy-glow transition transform hover:-translate-y-1">
                ✨ Coba Demo Gratis 14 Hari →
            </a>
            <a href="#demo" class="bg-white/80 backdrop-blur hover:bg-white border-2 border-candy-purple-200 hover:border-candy-purple-400 text-candy-purple-700 px-8 py-4 rounded-2xl text-base font-bold transition">
                👀 Lihat Demo Akun
            </a>
        </div>
        <div class="mt-14 flex items-center justify-center gap-6 text-sm text-gray-700 flex-wrap">
            <div class="flex items-center gap-2 bg-white/60 backdrop-blur px-4 py-2 rounded-full"><span class="text-candy-mint-600">✓</span> Tanpa kartu kredit</div>
            <div class="flex items-center gap-2 bg-white/60 backdrop-blur px-4 py-2 rounded-full"><span class="text-candy-mint-600">✓</span> Migrasi dari Talenta/Gadjian</div>
            <div class="flex items-center gap-2 bg-white/60 backdrop-blur px-4 py-2 rounded-full"><span class="text-candy-mint-600">✓</span> Server Indonesia 🇮🇩</div>
        </div>
    </div>
</section>

{{-- TRUST STRIP --}}
<section class="relative bg-white/60 backdrop-blur border-y border-candy-pink-100/50">
    <div class="max-w-7xl mx-auto px-6 py-14">
        <p class="text-center text-sm font-semibold text-candy-purple-600 mb-8 uppercase tracking-wider">Cocok untuk berbagai industri</p>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-6 text-center">
            @php $candyBgs = ['bg-candy-pink-50','bg-candy-purple-50','bg-brand-50','bg-candy-mint-50','bg-candy-yellow-50']; @endphp
            @foreach ($industries as $i => $ind)
                <div class="{{ $candyBgs[$i % 5] }} rounded-2xl p-5 hover:scale-105 transition transform shadow-sm hover:shadow-candy">
                    <div class="text-5xl mb-2">{{ $ind[0] }}</div>
                    <div class="font-bold text-gray-900">{{ $ind[1] }}</div>
                    <div class="text-xs text-gray-600 mt-1">{{ $ind[2] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- PROBLEM-SOLUTION --}}
<section class="relative bg-candy-pink py-20 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="blob blob-purple w-96 h-96 top-20 -right-20"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-6">
        <div class="text-center mb-14">
            <h2 class="text-4xl md:text-5xl font-extrabold">Dari <span class="text-candy-pink-500 line-through">Excel-Kacau</span> ke <span class="bg-clip-text text-transparent bg-gradient-to-r from-candy-purple-500 to-brand-500">Single Source of Truth</span></h2>
        </div>
        <div class="grid md:grid-cols-2 gap-6 max-w-5xl mx-auto">
            @foreach ($beforeAfter as $ba)
                <div class="bg-white/80 backdrop-blur rounded-3xl p-6 shadow-candy border border-white/50 hover:shadow-candy-lg transition">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-red-50/70 rounded-2xl p-4">
                            <div class="text-xs font-extrabold text-red-600 mb-2 uppercase tracking-wider">😩 Sebelum</div>
                            <p class="text-sm text-gray-700">{{ $ba[0] }}</p>
                        </div>
                        <div class="bg-candy-mint-50 rounded-2xl p-4">
                            <div class="text-xs font-extrabold text-candy-mint-600 mb-2 uppercase tracking-wider">🎉 Sesudah</div>
                            <p class="text-sm text-gray-900 font-semibold">{{ $ba[1] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- SCREENSHOT SHOWCASE — Alternating Real Screenshots --}}
@php
$screenshots = [
    [
        'image' => 'admin-payroll-runs.png',
        'title' => 'Payroll PPh 21 TER 2024 — Hitung 25 Karyawan dalam 30 Detik',
        'description' => 'Engine payroll lengkap dengan PMK 168/2023. Gross, Net, PPh 21, BPJS 5 program — semua terhitung otomatis dalam satu run. Audit-ready dengan calculation breakdown per komponen.',
        'bullets' => ['PPh 21 TER 2024 (kategori A/B/C lengkap)', 'BPJS 5 program dengan cap upah 2026', 'Gross / Nett / Gross-up methods', 'Bulk approve + generate slip PDF', 'Background queue untuk 500+ karyawan'],
        'reversed' => false,
    ],
    [
        'image' => 'admin-employees.png',
        'title' => 'Employee Database Indonesia-First',
        'description' => 'Master data lengkap dengan NIK 16-digit encrypted, NPWP, PTKP status (TK/0 sampai K/3), keluarga (BPJS dependent + PTKP dependent), bank account split, dokumen, expiry alert.',
        'bullets' => ['Custom field per perusahaan', 'Bulk import Excel + validation', 'QR code badge ID auto-generate', 'PII access logged (UU PDP compliance)', 'Multi-cabang dengan UMK per kota'],
        'reversed' => true,
    ],
    [
        'image' => 'admin-attendance-logs.png',
        'title' => 'Attendance Anti-Cheat dengan Mock GPS Detection',
        'description' => 'GPS + selfie + face recognition. Setiap attendance log dengan koordinat lat/long, foto selfie, dan flag 🚨 untuk mock GPS suspect. Live tracking dashboard untuk HRD monitoring real-time.',
        'bullets' => ['Geofence radius per cabang', 'Mock GPS detection (fake location)', 'Offline mode + auto-sync', 'Multi-method: GPS / Fingerprint / Face / QR', 'WhatsApp clock-in via share location'],
        'reversed' => false,
    ],
    [
        'image' => 'admin-leave-requests.png',
        'title' => 'Approval Workflow yang Manager Beneran Pakai',
        'description' => 'Inline action button "Setujui" / "Tolak" langsung di tabel — tidak perlu buka detail. Auto-update leave balance, notif ke karyawan. Multi-level approval dengan auto-escalation jika manager cuti.',
        'bullets' => ['Inline approve/reject buttons', 'Multi-level conditional routing', 'Auto-escalation timeout', 'Approval via WhatsApp reply "OK 1"', 'Audit trail per step'],
        'reversed' => true,
    ],
    [
        'image' => 'admin-dashboard.png',
        'title' => 'Dashboard Executive dengan AI-Ready Insight',
        'description' => 'Real-time stats: headcount aktif, kehadiran hari ini, telat alert, mock GPS warning, pending approval, payroll cost. Plus tren payroll 6 bulan & distribusi headcount per departemen.',
        'bullets' => ['6 stat cards dengan color-coded warning', 'Tren payroll line chart 6 bulan', 'Distribusi headcount per dept (doughnut)', 'AI narrative insight monthly (Phase 5)', 'Predictive turnover risk per karyawan'],
        'reversed' => false,
    ],
    [
        'image' => 'landing-pair-wizard.png',
        'title' => 'License-Once Self-host — Beli Sekali Pakai Selamanya',
        'description' => 'Drop-in license v3 dari whitelabel.co.id dengan RSA signing + AES-256 encrypted lock file. Self-host di server Anda sendiri, data 100% milik Anda, tanpa per-user fee bulanan.',
        'bullets' => ['Source code dilindungi license v3', 'Pairing wizard di /__pair', 'Data residency Indonesia (UU PDP)', 'Annual maintenance 20% (opsional)', 'Atau pilih SaaS Rp 10-25k/user/bulan'],
        'reversed' => true,
    ],
];
@endphp

<section id="features" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold tracking-tight">Semua yang Kamu Butuhkan, dalam Satu Sistem</h2>
            <p class="mt-4 text-lg text-gray-600">29 modul, 300+ fitur. Compliance Indonesia first-class.</p>
        </div>

        <div class="space-y-24">
            @foreach ($screenshots as $s)
                <div class="grid md:grid-cols-2 gap-12 items-center {{ $s['reversed'] ? 'md:[direction:rtl]' : '' }}">
                    <div class="{{ $s['reversed'] ? 'md:[direction:ltr]' : '' }}">
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl ring-1 ring-gray-200 bg-gray-50">
                            <img src="{{ asset('marketing/screenshots/' . $s['image']) }}"
                                 alt="{{ $s['title'] }}"
                                 loading="lazy"
                                 class="w-full h-auto" />
                        </div>
                    </div>
                    <div class="{{ $s['reversed'] ? 'md:[direction:ltr]' : '' }}">
                        <h3 class="text-3xl md:text-4xl font-bold tracking-tight leading-tight">{{ $s['title'] }}</h3>
                        <p class="mt-4 text-lg text-gray-600 leading-relaxed">{{ $s['description'] }}</p>
                        <ul class="mt-6 space-y-3">
                            @foreach ($s['bullets'] as $b)
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M16.7 5.3a1 1 0 010 1.4l-7 7a1 1 0 01-1.4 0l-3-3a1 1 0 011.4-1.4l2.3 2.3 6.3-6.3a1 1 0 011.4 0z"/>
                                    </svg>
                                    <span class="text-gray-700">{{ $b }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- COMPACT FEATURES GRID --}}
<section class="py-24 bg-candy-lavender">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">Plus Banyak <span class="bg-clip-text text-transparent bg-gradient-to-r from-candy-pink-500 to-candy-purple-500">Fitur Lainnya</span></h2>
            <p class="mt-3 text-gray-700">29 modul terhubung dalam satu ekosistem</p>
        </div>
        @php $cardColors = ['from-candy-pink-50 to-candy-pink-100','from-candy-purple-50 to-candy-purple-100','from-brand-50 to-brand-100','from-candy-mint-50 to-candy-mint-100','from-candy-blue-50 to-candy-blue-100','from-candy-yellow-50 to-candy-yellow-100','from-candy-pink-100 to-candy-purple-100','from-brand-100 to-candy-yellow-100','from-candy-mint-100 to-candy-blue-100']; @endphp
        <div class="grid md:grid-cols-3 gap-6">
            @foreach ($features as $i => $f)
                <div class="bg-gradient-to-br {{ $cardColors[$i % count($cardColors)] }} rounded-3xl p-6 hover:shadow-candy-lg hover:-translate-y-1 transition group">
                    <div class="text-5xl mb-4">{{ $f[0] }}</div>
                    <h3 class="text-xl font-extrabold mb-2 text-gray-900">{{ $f[1] }}</h3>
                    <p class="text-gray-700 text-sm mb-4">{{ $f[2] }}</p>
                    <ul class="space-y-2 text-sm">
                        @foreach ($f[3] as $b)
                            <li class="flex items-start gap-2">
                                <span class="text-candy-mint-600 mt-0.5">✓</span>
                                <span class="text-gray-800">{{ $b }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- DEMO ACCOUNTS --}}
<section id="demo" class="relative bg-gradient-to-br from-candy-purple-600 via-candy-pink-600 to-brand-600 text-white py-24 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-30">
        <div class="blob blob-yellow w-96 h-96 top-10 -left-20"></div>
        <div class="blob blob-mint w-96 h-96 bottom-10 -right-20"></div>
    </div>
    <div class="relative max-w-6xl mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl md:text-5xl font-extrabold">Coba Akun Demo <span class="text-candy-yellow-200">Sekarang</span></h2>
            <p class="mt-3 text-white/80 text-lg">8 role demo siap pakai. Login langsung tanpa daftar.</p>
        </div>
        <div class="overflow-x-auto rounded-3xl bg-white/10 backdrop-blur-xl ring-1 ring-white/20 shadow-candy-lg">
            <table class="w-full text-sm">
                <thead class="bg-white/10 text-left">
                    <tr class="text-white/90">
                        <th class="px-4 py-4">Role</th>
                        <th class="px-4 py-4">Email</th>
                        <th class="px-4 py-4">Password</th>
                        <th class="px-4 py-4">Cakupan</th>
                        <th class="px-4 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ($demoAccounts as $acc)
                        <tr class="hover:bg-white/10 transition">
                            <td class="px-4 py-3 font-semibold">{{ $acc[0] }}</td>
                            <td class="px-4 py-3 font-mono text-candy-yellow-200">{{ $acc[1] }}</td>
                            <td class="px-4 py-3 font-mono text-white/70">{{ $acc[2] }}</td>
                            <td class="px-4 py-3 text-white/80">{{ $acc[3] }}</td>
                            <td class="px-4 py-3"><a href="/admin/login" class="bg-white/20 hover:bg-white text-white hover:text-candy-purple-700 px-3 py-1.5 rounded-lg font-semibold transition">Login →</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

{{-- PRICING --}}
<section id="pricing" class="relative py-24 bg-candy-sunshine overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="blob blob-peach w-96 h-96 top-20 -left-20"></div>
        <div class="blob blob-pink w-72 h-72 bottom-20 right-10"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-6">
        <div class="text-center mb-14">
            <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight">Harga <span class="bg-clip-text text-transparent bg-gradient-to-r from-candy-pink-500 to-brand-500">Transparan</span>, Tanpa Lock-in</h2>
            <p class="mt-4 text-lg text-gray-700">Per karyawan per bulan. Atau license-once untuk self-host.</p>
        </div>
        @php $tierBg = ['bg-candy-pink','bg-candy-mint','bg-candy-lavender','bg-candy-sky']; @endphp
        <div class="grid md:grid-cols-4 gap-6">
            @foreach ($pricing as $i => $p)
                @php $highlighted = $p[5]; @endphp
                <div class="relative rounded-3xl p-6 transition transform hover:-translate-y-2 {{ $highlighted ? 'bg-gradient-to-br from-candy-pink-100 to-candy-purple-100 ring-4 ring-candy-pink-400 shadow-candy-lg scale-105 z-10' : 'bg-white/80 backdrop-blur shadow-candy ring-1 ring-white' }}">
                    @if ($highlighted)
                        <div class="absolute -top-3 right-6 bg-gradient-to-r from-candy-pink-500 via-candy-purple-500 to-brand-500 text-white text-xs px-3 py-1.5 rounded-full font-bold shadow-candy">⭐ RECOMMENDED</div>
                    @endif
                    <div class="font-extrabold text-xl text-gray-900">{{ $p[0] }}</div>
                    <div class="mt-4 flex items-baseline gap-1">
                        <span class="text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-candy-pink-500 to-brand-500">{{ $p[1] }}</span>
                        <span class="text-sm text-gray-600">{{ $p[2] }}</span>
                    </div>
                    <p class="text-sm text-gray-700 mt-2 mb-5">{{ $p[3] }}</p>
                    <ul class="space-y-2 text-sm mb-6">
                        @foreach ($p[4] as $feat)
                            <li class="flex items-start gap-2">
                                <span class="text-candy-mint-600 mt-0.5">✓</span>
                                <span class="text-gray-800">{{ $feat }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <a href="/admin/login" class="block w-full text-center py-3 rounded-xl font-bold transition {{ $highlighted ? 'bg-gradient-to-r from-candy-pink-500 via-candy-purple-500 to-brand-500 hover:shadow-candy-glow text-white shadow-candy' : 'bg-white hover:bg-candy-pink-50 text-candy-pink-700 ring-2 ring-candy-pink-200' }}">
                        Pilih {{ $p[0] }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FINAL CTA --}}
<section class="relative bg-gradient-to-br from-candy-pink-500 via-candy-purple-500 to-brand-500 text-white py-24 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-30">
        <div class="blob blob-yellow w-96 h-96 -top-20 left-1/4"></div>
        <div class="blob blob-mint w-96 h-96 -bottom-20 right-1/4"></div>
    </div>
    <div class="relative max-w-4xl mx-auto px-6 text-center">
        <div class="inline-block bg-white/20 backdrop-blur px-4 py-2 rounded-full text-sm font-bold mb-6">
            ✨ Siap dalam 5 menit
        </div>
        <h2 class="text-4xl md:text-6xl font-extrabold mb-6 leading-tight">Siap Stop Lembur<br>Tiap Akhir Bulan?</h2>
        <p class="text-xl text-white/90 mb-10 max-w-2xl mx-auto">Coba demo 14 hari gratis. Tanpa kartu kredit. Migrasi data ditanggung tim kami.</p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            <a href="/admin/login" class="bg-white text-candy-purple-700 hover:bg-candy-yellow-100 px-10 py-5 rounded-2xl font-extrabold shadow-candy-lg transition transform hover:-translate-y-1 text-lg">
                🚀 Mulai Demo Gratis
            </a>
            <a href="https://wa.me/6281234567890" class="bg-white/20 hover:bg-white/30 backdrop-blur border-2 border-white/40 px-10 py-5 rounded-2xl font-bold transition text-lg">
                💬 Konsultasi via WA
            </a>
        </div>
    </div>
</section>

@endsection
