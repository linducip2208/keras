@extends('marketing.layouts.app')

@section('title', $title)
@section('description', $description)

@push('jsonld')
<script type="application/ld+json">@php
echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'JobPosting',
    'title' => $job->title,
    'description' => strip_tags($job->description ?? ''),
    'datePosted' => optional($job->published_at)->toIso8601String(),
    'employmentType' => strtoupper($job->employment_type ?? 'FULL_TIME'),
    'jobLocation' => [
        '@type' => 'Place',
        'address' => ['@type' => 'PostalAddress', 'addressLocality' => $job->location ?? 'Indonesia', 'addressCountry' => 'ID'],
    ],
    'hiringOrganization' => ['@type' => 'Organization', 'name' => $job->company->name ?? 'HRIS Indonesia'],
    'baseSalary' => $job->salary_min ? [
        '@type' => 'MonetaryAmount',
        'currency' => $job->salary_currency ?? 'IDR',
        'value' => ['@type'=>'QuantitativeValue', 'minValue'=>(float)$job->salary_min, 'maxValue'=>(float)($job->salary_max ?? $job->salary_min), 'unitText'=>'MONTH'],
    ] : null,
], JSON_UNESCAPED_SLASHES);
@endphp</script>
@endpush

@section('content')

<section class="py-12 bg-gradient-to-b from-brand-50 to-white">
    <div class="max-w-3xl mx-auto px-6">
        <a href="{{ route('career.index') }}" class="text-brand-600 hover:underline text-sm">← Kembali ke daftar lowongan</a>
        <div class="mt-3 text-sm text-gray-500">{{ $job->company->name ?? '' }} @if($job->department) · {{ $job->department->name }} @endif</div>
        <h1 class="mt-2 text-3xl md:text-4xl font-bold leading-tight">{{ $job->title }}</h1>
        <div class="mt-4 flex flex-wrap gap-3 text-sm">
            @if ($job->location)
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-white border border-gray-200 rounded-full">📍 {{ $job->location }}</span>
            @endif
            @if ($job->employment_type)
                <span class="inline-flex items-center px-3 py-1 bg-brand-100 text-brand-800 rounded-full font-medium">{{ strtoupper($job->employment_type) }}</span>
            @endif
            @if ($job->salary_min)
                <span class="inline-flex items-center px-3 py-1 bg-green-50 text-green-700 rounded-full font-medium">
                    💰 Rp {{ number_format($job->salary_min, 0, ',', '.') }}
                    @if ($job->salary_max) – Rp {{ number_format($job->salary_max, 0, ',', '.') }} @endif
                </span>
            @endif
        </div>
    </div>
</section>

<section class="py-16">
    <div class="max-w-3xl mx-auto px-6 grid md:grid-cols-3 gap-8">
        <div class="md:col-span-2 prose prose-lg max-w-none">
            <h2>Deskripsi Pekerjaan</h2>
            <div>{!! nl2br(e($job->description ?? '-')) !!}</div>

            <h2>Persyaratan</h2>
            <div>{!! nl2br(e($job->requirements ?? '-')) !!}</div>
        </div>

        <aside class="md:col-span-1">
            <div class="sticky top-24 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="font-bold mb-3">Apply Sekarang</h3>
                <p class="text-sm text-gray-600 mb-4">Upload CV + isi form singkat. Tim HR akan respond dalam 3-7 hari kerja.</p>
                <a href="#apply" class="block w-full text-center bg-brand-600 hover:bg-brand-700 text-white py-3 rounded-lg font-semibold">Apply Now ↓</a>
            </div>
        </aside>
    </div>
</section>

<section id="apply" class="py-16 bg-gray-50">
    <div class="max-w-3xl mx-auto px-6">
        <h2 class="text-2xl font-bold mb-6">Form Lamaran</h2>

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('career.apply', $job->public_token) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-gray-200 rounded-2xl p-6 space-y-4">
            @csrf
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Lengkap *</label>
                    <input type="text" name="full_name" required value="{{ old('full_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-brand-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email *</label>
                    <input type="email" name="email" required value="{{ old('email') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-brand-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nomor HP *</label>
                    <input type="tel" name="phone" required value="{{ old('phone') }}" placeholder="081234567890"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-brand-500 focus:outline-none" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Lokasi Saat Ini</label>
                    <input type="text" name="location" value="{{ old('location') }}" placeholder="Jakarta, Bandung, dll."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Posisi Saat Ini</label>
                    <input type="text" name="current_position" value="{{ old('current_position') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Pengalaman (tahun)</label>
                    <input type="number" name="years_experience" min="0" max="50" value="{{ old('years_experience') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Salary Expectation (Rp/bln)</label>
                    <input type="number" name="salary_expectation" min="0" value="{{ old('salary_expectation') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Notice Period (hari)</label>
                    <input type="number" name="notice_period_days" min="0" max="365" value="{{ old('notice_period_days') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">LinkedIn URL</label>
                <input type="url" name="linkedin_url" value="{{ old('linkedin_url') }}" placeholder="https://linkedin.com/in/..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Cover Letter (opsional)</label>
                <textarea name="cover_letter" rows="4" placeholder="Ceritakan kenapa kamu cocok untuk posisi ini..."
                          class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('cover_letter') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">CV / Resume * (PDF, DOC, DOCX, max 5MB)</label>
                <input type="file" name="cv" accept=".pdf,.doc,.docx" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 file:mr-3 file:bg-brand-50 file:border-0 file:px-3 file:py-1 file:rounded file:text-brand-700" />
            </div>

            <label class="flex items-start gap-2 text-sm text-gray-600">
                <input type="checkbox" name="consent" required class="mt-0.5" />
                <span>Saya setuju data saya disimpan & diproses sesuai <a href="/privacy" class="text-brand-600 underline">UU PDP 27/2022</a>.</span>
            </label>

            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white py-3 rounded-lg font-bold">
                Kirim Lamaran →
            </button>
        </form>
    </div>
</section>

@endsection
