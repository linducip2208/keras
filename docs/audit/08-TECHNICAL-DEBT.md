# 08 — Technical Debt

> Daftar jujur sisa pekerjaan. Prioritas: P1 (correctness) → P2 (security
> hardening) → P3 (fitur). Tidak ada yang diklaim selesai.

## P1

1. T-2: unique per-tenant (assets.code, invoices.number, payments.reference) — butuh audit data.
2. T-11: enkripsi/masking `disbursement_items.account_number` (keputusan PDP).
3. Rekonsiliasi PPh progresif Desember (TER Jan–Nov + hitung ulang tahunan).
4. Validasi usia JP (56 th) + range JKK di BPJSCalculator.
5. PDF slip tanpa enkripsi — butuh post-process qpdf / signed URL expiry.

## P2

6. Login throttling + lockout.
7. Verifikasi signature webhook payment & WA per-vendor.
8. Spatie `teams` untuk roles per-tenant.
9. Geofence radius attendance.
10. Scheduler nyata: eskalasi approval, akrual cuti, pengingat payroll, retry webhook.
11. T-1 FK `business_trip_id`, T-6/T-8 index, T-10 nomor unik.

## P3

12. Sanctum rotation/refresh + biometric gate.
13. FCM push + deep-link approval + offline queue Hive.
14. PDF parser untuk CV screening (`NEEDS-PDF-PARSER` marker aktif di kode).
15. Direct submit DJP Coretax & SIPP (menunggu API pemerintah live).
16. Modul PLANNED: Onboarding, Kompetensi, LMS, Travel, E-Sign, report builder.
17. `eval()` formula payroll → expression parser library + audit log.
18. `attendance_based *22` → sinkronisasi hari hadir aktual.
