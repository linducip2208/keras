<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProgrammaticSeoController extends Controller
{
    public const INDUSTRIES = [
        'manufaktur' => 'Manufaktur', 'f-b' => 'F&B', 'retail' => 'Retail',
        'startup' => 'Startup & Tech', 'klinik' => 'Klinik & Healthcare',
        'kontraktor' => 'Konstruksi', 'hospitality' => 'Hospitality',
        'sekolah' => 'Pendidikan', 'koperasi' => 'Koperasi', 'logistik' => 'Logistik',
    ];

    public const CITIES = [
        'jakarta' => 'Jakarta', 'surabaya' => 'Surabaya', 'bandung' => 'Bandung',
        'medan' => 'Medan', 'semarang' => 'Semarang', 'makassar' => 'Makassar',
        'palembang' => 'Palembang', 'batam' => 'Batam', 'pekanbaru' => 'Pekanbaru',
        'denpasar' => 'Denpasar', 'bogor' => 'Bogor', 'tangerang' => 'Tangerang',
        'bekasi' => 'Bekasi', 'depok' => 'Depok', 'malang' => 'Malang',
        'yogyakarta' => 'Yogyakarta', 'solo' => 'Solo', 'banjarmasin' => 'Banjarmasin',
        'balikpapan' => 'Balikpapan', 'manado' => 'Manado',
    ];

    public const COMPETITORS = [
        'talenta' => ['name' => 'Mekari Talenta', 'positioning' => 'Market leader Indonesia'],
        'gadjian' => ['name' => 'Gadjian', 'positioning' => 'UKM payroll-first'],
        'sleekr' => ['name' => 'Sleekr HR', 'positioning' => 'Bagian Mekari, ramah pemula'],
        'linovhr' => ['name' => 'LinovHR', 'positioning' => 'Enterprise on-premise'],
        'broadwayshr' => ['name' => 'BroadwaysHR', 'positioning' => 'SMB payroll + absensi'],
        'kantorku' => ['name' => 'KantorKu by Dealls', 'positioning' => 'ISO 27001, dedicated AM'],
        'karyaone' => ['name' => 'KaryaONE', 'positioning' => 'UKM/UMKM/koperasi'],
    ];

    public function bestHris(?string $year = null)
    {
        $year = $year ?: (string) date('Y');
        return view('pseo.best-hris', [
            'year' => $year,
            'competitors' => self::COMPETITORS,
            'title' => "Best Aplikasi HRIS Indonesia {$year} — Perbandingan Lengkap 8 Software",
            'description' => "Daftar HRIS terbaik {$year} di Indonesia: Mekari Talenta, Gadjian, LinovHR, KantorKu, dan lainnya. Bandingkan fitur, harga, & differentiator.",
        ]);
    }

    public function bestHrisForIndustry(string $industry)
    {
        if (!isset(self::INDUSTRIES[$industry])) abort(404);
        $industryName = self::INDUSTRIES[$industry];

        return view('pseo.industry', [
            'industry_slug' => $industry,
            'industry_name' => $industryName,
            'title' => "HRIS Terbaik untuk {$industryName} Indonesia 2026",
            'description' => "Rekomendasi aplikasi HRIS untuk industri {$industryName}. Shift management, payroll, BPJS, attendance — semua spesifik untuk kebutuhan {$industryName}.",
        ]);
    }

    public function hrisForCity(string $city)
    {
        if (!isset(self::CITIES[$city])) abort(404);
        $cityName = self::CITIES[$city];

        return view('pseo.city', [
            'city_slug' => $city,
            'city_name' => $cityName,
            'title' => "Aplikasi HRIS untuk Perusahaan di {$cityName} 2026",
            'description' => "Software HRIS terbaik untuk perusahaan di {$cityName}. Lokal Indonesia, server data residency, sesuai UMK {$cityName}.",
        ]);
    }

    public function compare(string $competitor)
    {
        if (!isset(self::COMPETITORS[$competitor])) abort(404);
        $comp = self::COMPETITORS[$competitor];

        return view('pseo.compare', [
            'competitor_slug' => $competitor,
            'competitor' => $comp,
            'title' => "HRIS Indonesia vs {$comp['name']} — Perbandingan Fitur, Harga, Differentiator",
            'description' => "Perbandingan head-to-head HRIS kami vs {$comp['name']}. Lihat siapa lebih cocok untuk perusahaanmu — fitur, harga, BYOK, license-once.",
        ]);
    }

    public function alternatives(string $competitor)
    {
        if (!isset(self::COMPETITORS[$competitor])) abort(404);
        $comp = self::COMPETITORS[$competitor];

        return view('pseo.alternatives', [
            'competitor_slug' => $competitor,
            'competitor' => $comp,
            'title' => "Alternatif {$comp['name']} untuk HRIS Indonesia 2026",
            'description' => "Cari alternatif {$comp['name']}? HRIS Indonesia menawarkan BYOK, license-once, WhatsApp bot ESS — semua yang {$comp['name']} tidak punya.",
        ]);
    }
}
