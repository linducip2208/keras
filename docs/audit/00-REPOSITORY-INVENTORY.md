# 00 — Repository Inventory

> Baseline audit KERAS HRIS. Dihasilkan dari 5 audit paralel (backend, database,
> Filament, Flutter, security) + verifikasi integrator. Tanggal: 2026-11-26.

## Struktur repository

| Path | Isi | Status |
|---|---|---|
| `app/` | Models (~60), Services (13), Http (Api/Web/Webhook), Filament (46 resources), Adapters, Providers, Console | Aktif dikembangkan |
| `bootstrap/app.php` | Middleware web+api, alias `tenant.api` | ✅ diperbaiki (tenant binding API) |
| `config/` | attendance, billing (baru), + bawaan Laravel | ✅ 2 config baru |
| `database/migrations/` | 21 file, ±95 tabel, 29 modul ter-cover | ✅ |
| `database/seeders/` | DemoTenant/Employees/Attendance/Payroll/TaxAndBPJS/Holiday/Plan aktif; Billing/JobPostings/Applications TIDAK dipanggil | ⚠️ parsial |
| `docker/` | nginx + php | Ada, belum diuji build di sini |
| `docs/` | FEATURES.md, 00-11, audit/, payroll/ (baru) | Diperbarui bertahap |
| `flutter_app/` | 10 file lib + test, pubspec | ✅ analyze bersih, test pass |
| `public/`, `resources/` | Landing, blade Filament, PDF views | Valid UTF-8 |
| `routes/` | api.php (16 endpoint), web.php, console.php (stub), pair-routes.php | ⚠️ console kosong |
| `scripts/` | Screenshot Playwright | OK |
| `storage/`, `tests/` | 16 test hijau (6 overtime, 3 TER, 3 BPJS, 2 tenant isolation, 2 scaffold) | ✅ coverage inti baru |
| `composer.json` | PHP 8.3, Laravel 13.8, Filament 4.11, Sanctum 4.3 | — |
| `package.json` | Vite 8, Tailwind 4, Playwright | — |
| `.env.example` | SQLite dev default | ⚠️ tambah BILLING_PPN_RATE, ATTENDANCE_REJECT_MOCK_GPS |

## Kebenaran yang dikunci

1. Semua 29 modul punya tabel; ±40 tabel tanpa model (Onboarding, LMS, Kompetensi, Travel, Analytics, ATS-lanjutan).
2. Uang selalu `decimal`, tidak ada float — pertahankan.
3. Klaim palsu yang sudah dihapus: selfie path lokal, password PDF, CV screening tanpa isi, `now()` sebagai periode, acme fallback.
4. Test scaffold `ExampleTest` diperbarui: `/` redirect ke `/__pair` saat belum pairing (by design).
