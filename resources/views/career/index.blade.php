@extends('marketing.layouts.app')

@section('title', $title)
@section('description', $description)

@push('jsonld')
<script type="application/ld+json">@php
$jobsLd = [];
foreach ($jobs as $j) {
    $jobsLd[] = [
        '@type' => 'JobPosting',
        'title' => $j->title,
        'description' => strip_tags($j->description ?? ''),
        'datePosted' => optional($j->published_at)->toIso8601String(),
        'employmentType' => strtoupper($j->employment_type ?? 'FULL_TIME'),
        'jobLocation' => [
            '@type' => 'Place',
            'address' => ['@type' => 'PostalAddress', 'addressLocality' => $j->location ?? 'Indonesia', 'addressCountry' => 'ID'],
        ],
        'hiringOrganization' => ['@type' => 'Organization', 'name' => $j->company->name ?? 'HRIS Indonesia'],
    ];
}
echo json_encode(['@context'=>'https://schema.org','@graph'=>$jobsLd], JSON_UNESCAPED_SLASHES);
@endphp</script>
@endpush

@section('content')

<section class="py-20 bg-gradient-to-b from-brand-50 to-white">
    <div class="max-w-4xl mx-auto px-6 text-center">
        @if ($mode === 'tenant' && isset($tenant))
            <div class="text-sm text-brand-600 mb-3">
                <a href="{{ route('career.index') }}" class="hover:underline">← Marketplace</a>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">Karir di {{ $tenant->name }}</h1>
            <p class="mt-6 text-lg text-gray-600">{{ count($jobs) }} lowongan kerja terbuka. Apply langsung — upload CV, isi form, tunggu kabar.</p>
        @else
            <div class="inline-block bg-brand-100 text-brand-800 px-4 py-1.5 rounded-full text-sm font-medium mb-4">
                🚀 Job Marketplace Indonesia
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">Cari Kerja di Perusahaan Indonesia</h1>
            <p class="mt-6 text-lg text-gray-600">{{ count($jobs) }} lowongan dari {{ $companies->count() }} perusahaan. Apply langsung dengan upload CV — no daftar required.</p>
        @endif
    </div>
</section>

@if ($mode === 'marketplace' && $companies->isNotEmpty())
<section class="py-12 bg-white border-b border-gray-200">
    <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-xl font-bold mb-4">Browse per Perusahaan</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach ($companies as $c)
                <a href="{{ route('career.tenant', $c->slug) }}"
                   class="block p-4 bg-gray-50 hover:bg-brand-50 hover:ring-2 hover:ring-brand-300 rounded-xl transition">
                    <div class="font-semibold text-gray-900">{{ $c->name }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $c->job_count }} lowongan</div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="py-16">
    <div class="max-w-4xl mx-auto px-6">
        @if ($jobs->isEmpty())
            <div class="text-center py-16">
                <div class="text-6xl mb-4">🔍</div>
                <h2 class="text-2xl font-bold mb-2">Belum ada lowongan terbuka</h2>
                <p class="text-gray-600">Cek lagi nanti, atau lihat lowongan di perusahaan lain.</p>
                @if ($mode === 'tenant')
                    <a href="{{ route('career.index') }}" class="inline-block mt-4 bg-brand-600 hover:bg-brand-700 text-white px-6 py-3 rounded-lg font-semibold">Lihat Marketplace</a>
                @endif
            </div>
        @else
            <div class="space-y-4">
                @foreach ($jobs as $job)
                    <a href="{{ route('career.show', $job->public_token) }}"
                       class="block bg-white border border-gray-200 hover:border-brand-300 hover:shadow-lg rounded-2xl p-6 transition">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                                    <span class="font-medium">{{ $job->company->name ?? 'Perusahaan' }}</span>
                                    @if ($job->department)
                                        <span>·</span>
                                        <span>{{ $job->department->name }}</span>
                                    @endif
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $job->title }}</h3>
                                <div class="flex flex-wrap items-center gap-2 mt-2 text-sm">
                                    @if ($job->location)
                                        <span class="inline-flex items-center gap-1 text-gray-600">📍 {{ $job->location }}</span>
                                    @endif
                                    @if ($job->employment_type)
                                        <span class="px-2 py-0.5 bg-brand-50 text-brand-700 rounded text-xs font-medium">{{ strtoupper($job->employment_type) }}</span>
                                    @endif
                                    @if ($job->salary_min)
                                        <span class="text-gray-600 text-xs">
                                            💰 Rp {{ number_format($job->salary_min, 0, ',', '.') }}
                                            @if ($job->salary_max) – Rp {{ number_format($job->salary_max, 0, ',', '.') }} @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-brand-600 text-2xl">→</div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

@endsection
