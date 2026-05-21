@extends('marketing.layouts.app')
@section('title', $title)
@section('description', $description)

@push('jsonld')
<script type="application/ld+json">@php echo json_encode(['@context'=>'https://schema.org','@type'=>'WebPage','name'=>$title,'description'=>$description], JSON_UNESCAPED_SLASHES); @endphp</script>
@endpush

@section('content')

<section class="py-20 bg-gradient-to-b from-brand-50 to-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">HRIS Terbaik untuk Industri <span class="text-brand-600">{{ $industry_name }}</span></h1>
        <p class="mt-6 text-lg text-gray-600">Fitur HRIS yang spesifik dirancang untuk kebutuhan operasional industri {{ $industry_name }} di Indonesia.</p>
        <a href="/admin/login" class="inline-block mt-8 bg-brand-600 hover:bg-brand-700 text-white px-8 py-4 rounded-xl font-bold shadow-lg">Coba Demo Sekarang →</a>
    </div>
</section>

<section class="py-16">
    <div class="max-w-4xl mx-auto px-6 prose prose-lg">
        <h2>Mengapa Industri {{ $industry_name }} Butuh HRIS Khusus?</h2>
        <p>Industri {{ $industry_name }} di Indonesia punya karakteristik unik dalam manajemen SDM: shift rotasi yang kompleks, perhitungan lembur sesuai Kepmenaker 102/2004, sertifikasi keahlian (K3, kompetensi), turnover yang tinggi, dan kebutuhan akan attendance yang akurat di multi-lokasi.</p>

        <p>HRIS yang generic seringkali tidak fit karena tidak mengakomodasi pola kerja {{ $industry_name }}. Kami membangun fitur khusus untuk industri ini:</p>

        @if ($industry_slug === 'manufaktur')
            <h3>Fitur Khusus Manufaktur</h3>
            <ul>
                <li><strong>Shift 3-rotasi otomatis</strong> — pagi/siang/malam dengan pattern builder unlimited</li>
                <li><strong>Production line attendance</strong> — fingerprint device SDK (ZKTeco, Suprema) di setiap lini</li>
                <li><strong>Overtime Kepmenaker 102</strong> — auto-calculate jam 1 = 1.5x, jam 2+ = 2x, weekend = 2-4x</li>
                <li><strong>K3 training tracking</strong> — sertifikasi expiry alert + auto-enroll refresher</li>
                <li><strong>BPJS JKK risk tinggi</strong> — auto-calculate iuran 1.74% untuk industri high-risk</li>
                <li><strong>UMK per cabang</strong> — perusahaan multi-pabrik lintas kota dengan UMK berbeda</li>
            </ul>
        @elseif ($industry_slug === 'f-b' || $industry_slug === 'retail')
            <h3>Fitur Khusus F&B & Retail</h3>
            <ul>
                <li><strong>Multi-outlet attendance</strong> — geofence per outlet, GPS + selfie wajib</li>
                <li><strong>Split shift</strong> — break panjang siang (jam 11-15) yang tidak dihitung kerja</li>
                <li><strong>Part-time + freelance</strong> — payroll per jam, prorate otomatis</li>
                <li><strong>Tipping pool</strong> — distribusi tips antar staff per shift</li>
                <li><strong>Komisi penjualan</strong> — auto-calculate per outlet, per kategori produk</li>
                <li><strong>WhatsApp clock-in</strong> — untuk karyawan yang tidak punya smartphone</li>
            </ul>
        @elseif ($industry_slug === 'startup')
            <h3>Fitur Khusus Startup & Tech</h3>
            <ul>
                <li><strong>OKR cascading</strong> — quarterly goals dari C-level cascade ke individual contributor</li>
                <li><strong>360° performance review</strong> — self + peer + manager + subordinate, 9-box matrix</li>
                <li><strong>Continuous feedback</strong> — kudos antar karyawan anytime</li>
                <li><strong>AI CV screening</strong> — BYOK OpenAI/DeepSeek untuk filter ratusan lamaran</li>
                <li><strong>Equity / RSU tracking</strong> — di-roadmap untuk Phase 6</li>
                <li><strong>Flexible WFA</strong> — no geofence, attendance hanya selfie + check-in time</li>
            </ul>
        @elseif ($industry_slug === 'klinik')
            <h3>Fitur Khusus Klinik & Healthcare</h3>
            <ul>
                <li><strong>Shift 24-7 + on-call</strong> — rotasi pagi/sore/malam + standby dokter</li>
                <li><strong>Sertifikasi medis expiry</strong> — STR, SIP dokter, SIPP perawat auto-alert</li>
                <li><strong>BPJS JKK risk medium</strong> — sektor kesehatan terutama Covid post-pandemic</li>
                <li><strong>Komisi dokter visit</strong> — bagi hasil per pasien kunjungan</li>
                <li><strong>Privacy strong</strong> — UU PDP compliance untuk data pasien yang terkait HR</li>
            </ul>
        @else
            <h3>Fitur Khusus untuk {{ $industry_name }}</h3>
            <ul>
                <li>Shift management yang sesuai pola kerja industri</li>
                <li>Compliance tax & BPJS lengkap untuk Indonesia</li>
                <li>Multi-cabang dengan UMK per kota</li>
                <li>Attendance dengan GPS + selfie + anti-cheat</li>
                <li>Payroll otomatis dengan komponen industri-spesifik</li>
            </ul>
        @endif

        <h2>Bandingkan dengan Software Lain</h2>
        <p>Sebagian besar HRIS generic di pasar tidak punya fitur spesifik untuk {{ $industry_name }}. Sebelum memilih, bandingkan kami dengan <a href="/compare/hris-vs-talenta">Mekari Talenta</a>, <a href="/compare/hris-vs-gadjian">Gadjian</a>, dan <a href="/compare/hris-vs-linovhr">LinovHR</a>.</p>

        <h2>Cara Memulai</h2>
        <p>Coba demo gratis 14 hari, tanpa kartu kredit. Tim kami akan bantu migrasi data dari Excel atau HRIS lama. Atau pilih license-once jika ingin self-host di server perusahaan.</p>

        <p><a href="/admin/login" class="inline-block bg-brand-600 hover:bg-brand-700 text-white px-6 py-3 rounded-lg font-semibold no-underline">Mulai Demo Gratis →</a></p>
    </div>
</section>

@endsection
