@extends('marketing.layouts.app')

@section('title', 'Lamaran Terkirim — ' . $job->title)
@section('description', 'Terima kasih sudah apply. Tim HR akan respond segera.')

@section('content')

<section class="py-24 bg-gradient-to-b from-green-50 to-white">
    <div class="max-w-2xl mx-auto px-6 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
            <svg class="w-10 h-10 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M16.7 5.3a1 1 0 010 1.4l-7 7a1 1 0 01-1.4 0l-3-3a1 1 0 011.4-1.4l2.3 2.3 6.3-6.3a1 1 0 011.4 0z"/>
            </svg>
        </div>
        <h1 class="text-4xl font-extrabold mb-4">Lamaran Terkirim! 🎉</h1>
        <p class="text-lg text-gray-600 mb-8">
            Terima kasih sudah apply untuk posisi <strong>{{ $job->title }}</strong>.
            Tim HR akan review CV & cover letter Anda dalam 3-7 hari kerja, dan akan menghubungi via email jika cocok.
        </p>
        <div class="bg-white border border-gray-200 rounded-2xl p-6 text-left">
            <h3 class="font-bold mb-3">Apa yang Terjadi Selanjutnya?</h3>
            <ol class="space-y-2 text-sm text-gray-700 list-decimal pl-5">
                <li><strong>Screening CV</strong> — AI + recruiter review CV Anda (1-3 hari kerja)</li>
                <li><strong>Phone/Video Interview</strong> — Jika lolos screening (3-5 hari kerja)</li>
                <li><strong>Interview Lanjutan</strong> — Dengan hiring manager + tim teknis</li>
                <li><strong>Offer Letter</strong> — Final decision + negotiation</li>
            </ol>
        </div>
        <div class="mt-8 flex gap-4 justify-center">
            <a href="{{ route('career.index') }}" class="border border-gray-300 hover:border-gray-400 px-6 py-3 rounded-lg font-semibold">Lihat Lowongan Lain</a>
            <a href="/" class="bg-brand-600 hover:bg-brand-700 text-white px-6 py-3 rounded-lg font-semibold">Kembali ke Beranda</a>
        </div>
    </div>
</section>

@endsection
