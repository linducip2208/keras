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
        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">Karir di Perusahaan Indonesia</h1>
        <p class="mt-6 text-lg text-gray-600">{{ count($jobs) }} lowongan kerja terbuka. Apply langsung di sini — upload CV, isi form, tunggu kabar.</p>
    </div>
</section>

<section class="py-16">
    <div class="max-w-4xl mx-auto px-6">
        @if ($jobs->isEmpty())
            <div class="text-center py-16">
                <div class="text-6xl mb-4">🔍</div>
                <h2 class="text-2xl font-bold mb-2">Belum ada lowongan terbuka</h2>
                <p class="text-gray-600">Cek lagi nanti, atau ikuti kami di media sosial untuk update lowongan terbaru.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($jobs as $job)
                    <a href="{{ route('career.show', $job->public_token) }}"
                       class="block bg-white border border-gray-200 hover:border-brand-300 hover:shadow-lg rounded-2xl p-6 transition">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                                    <span>{{ $job->company->name ?? 'Perusahaan' }}</span>
                                    @if ($job->department)
                                        <span>·</span>
                                        <span>{{ $job->department->name }}</span>
                                    @endif
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $job->title }}</h3>
                                <div class="flex flex-wrap items-center gap-2 mt-2 text-sm">
                                    @if ($job->location)
                                        <span class="inline-flex items-center gap-1 text-gray-600">
                                            📍 {{ $job->location }}
                                        </span>
                                    @endif
                                    @if ($job->employment_type)
                                        <span class="px-2 py-0.5 bg-brand-50 text-brand-700 rounded text-xs">
                                            {{ strtoupper($job->employment_type) }}
                                        </span>
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
