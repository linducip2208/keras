@extends('marketing.layouts.app')
@section('title', $title)
@section('description', $description)

@push('jsonld')
<script type="application/ld+json">@php echo json_encode(['@context'=>'https://schema.org','@type'=>'LocalBusiness','name'=>"HRIS Indonesia - {$city_name}",'address'=>['@type'=>'PostalAddress','addressLocality'=>$city_name,'addressCountry'=>'ID']], JSON_UNESCAPED_SLASHES); @endphp</script>
@endpush

@section('content')

<section class="py-20 bg-gradient-to-b from-brand-50 to-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">Aplikasi HRIS untuk Perusahaan di <span class="text-brand-600">{{ $city_name }}</span></h1>
        <p class="mt-6 text-lg text-gray-600">Software HR & payroll Indonesia dengan server lokal, sesuai UMK {{ $city_name }}, compliance PPh 21 TER + BPJS.</p>
        <a href="/admin/login" class="inline-block mt-8 bg-brand-600 hover:bg-brand-700 text-white px-8 py-4 rounded-xl font-bold shadow-lg">Coba Gratis 14 Hari →</a>
    </div>
</section>

<section class="py-16">
    <div class="max-w-4xl mx-auto px-6 prose prose-lg">
        <h2>HRIS yang Mengerti Bisnis di {{ $city_name }}</h2>
        <p>Perusahaan di {{ $city_name }} punya tantangan unik dalam pengelolaan SDM: UMK kota yang spesifik, kompetisi talent yang ketat, dan kebutuhan akan compliance lokal Indonesia. HRIS Indonesia hadir untuk menyederhanakan semua itu.</p>

        <h3>Fitur yang Sesuai untuk Perusahaan {{ $city_name }}</h3>
        <ul>
            <li><strong>UMK {{ $city_name }} ter-update</strong> — BPJS dihitung sesuai UMK lokal, otomatis</li>
            <li><strong>Multi-cabang dalam kota</strong> — geofence GPS per cabang/outlet di {{ $city_name }}</li>
            <li><strong>Compliance pajak Indonesia</strong> — PPh 21 TER 2024 (PMK 168/2023) auto-update</li>
            <li><strong>BPJS lengkap</strong> — Kesehatan + JHT + JP + JKK + JKM, file SIPP siap upload</li>
            <li><strong>Server di Indonesia</strong> — data residency sesuai UU PDP 27/2022</li>
            <li><strong>Mobile app + WhatsApp bot</strong> — karyawan di lapangan tetap connected</li>
            <li><strong>License-once tersedia</strong> — beli sekali, self-host di server perusahaan</li>
        </ul>

        <h3>Industri yang Sudah Pakai HRIS di {{ $city_name }}</h3>
        <p>HRIS Indonesia digunakan oleh berbagai perusahaan di {{ $city_name }} dari berbagai industri:</p>
        <ul>
            <li><a href="/best-aplikasi-hris-untuk-manufaktur">Manufaktur</a> — pabrik dengan shift 3-rotasi</li>
            <li><a href="/best-aplikasi-hris-untuk-f-b">F&B & Retail</a> — chain multi-outlet</li>
            <li><a href="/best-aplikasi-hris-untuk-startup">Startup & Tech</a> — OKR + 360 review</li>
            <li><a href="/best-aplikasi-hris-untuk-klinik">Klinik & Healthcare</a> — shift 24-7</li>
        </ul>

        <h2>Harga untuk Perusahaan {{ $city_name }}</h2>
        <p>Mulai dari <strong>Rp 10.000/karyawan/bulan</strong> untuk paket Starter (≤25 karyawan), atau license-once mulai Rp 75 juta untuk self-host. Tidak ada biaya setup, tidak ada biaya migrasi.</p>

        <p><a href="/admin/login" class="inline-block bg-brand-600 hover:bg-brand-700 text-white px-6 py-3 rounded-lg font-semibold no-underline">Mulai Demo Sekarang →</a></p>
    </div>
</section>

@endsection
