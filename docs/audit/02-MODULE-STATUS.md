# 02 — Module Status (29 Modul)

> Verdict = kondisi KODE, bukan jumlah file. DoD: DB benar, backend jalan,
> UI jalan, CRUD, business process, validasi, auth, tenant isolation, audit
> trail, integrasi, error handling, test, dokumentasi.

| # | Modul | Status awal | Perubahan | Status akhir |
|---|---|---|---|---|
| 1 | Employee DB | PARTIAL | +relasi, +test isolasi | PARTIAL (tanpa e-sign/QR-cetak) |
| 2 | Organization | PARTIAL | — | PARTIAL |
| 3 | Contract | PARTIAL | — | PARTIAL (tanpa e-sign flow) |
| 4 | HR Letter | PARTIAL | — | PARTIAL |
| 5 | Approval Workflow | PARTIAL | +guard idempoten, +action panel, +Steps RM, +relasi | COMPLETE (minus eskalasi otomatis/scheduler) |
| 6 | Announcement | PARTIAL | — | PARTIAL |
| 7 | ESS / Docs | PARTIAL | +WA honest msg | PARTIAL |
| 8 | E-Sign | PLANNED | — | PLANNED (hanya opsi dropdown adapter) |
| 9 | Shift | PARTIAL | dipakai attendance (schedule → shift) | PARTIAL |
| 10 | Attendance | PARTIAL | +tolak mock-GPS, +shift dari jadwal, +upload selfie endpoint, +mobile multipart | COMPLETE (minus geofence radius) |
| 11 | Overtime | PARTIAL | +6 unit test Kepmenaker | COMPLETE (formula) |
| 12 | Leave | PARTIAL | +IDOR fix, +atomic balance, +mobile form, +WA tenant guard | COMPLETE (minus akrual otomatis) |
| 13 | Holiday | PARTIAL | — | PARTIAL |
| 14 | Payroll | PARTIAL | +TER fix, +periode real, +null guard, +wizard hardening, +slipNumber periode | PARTIAL (minus lock flow UI, minus scheduler) |
| 15 | PPh 21/26 | PARTIAL | +setYear, +3 unit test, +resource read-only | PARTIAL (minus rekonsiliasi Des/progresif) |
| 16 | BPJS | PARTIAL | +setYear, +3 unit test, +resource read-only | PARTIAL (minus validasi usia JP) |
| 17 | Disbursement | PARTIAL | — | PARTIAL (batch file SAMPLE, belum terverifikasi bank) |
| 18 | Reimbursement | PARTIAL | — | PARTIAL |
| 19 | Recruitment/ATS | PARTIAL | +CV baca docx/txt, +tenant guard adapter, +blade UTF-8 OK | PARTIAL (PDF parser NEEDS) |
| 20 | Onboarding | PLANNED | tabel ada, model nol | PLANNED |
| 21 | OKR/KPI | PARTIAL | +KeyResults RM | PARTIAL |
| 22 | Appraisal | PARTIAL | — | PARTIAL |
| 23 | Competency | PLANNED | tabel ada, model nol | PLANNED |
| 24 | LMS | PLANNED | tabel ada, model nol | PLANNED |
| 25 | Asset | PARTIAL | — | PARTIAL |
| 26 | Travel | PLANNED | tabel ada, model nol | PLANNED |
| 27 | Loan | PARTIAL | — | PARTIAL |
| 28 | Exit | PARTIAL | — | PARTIAL |
| 29 | Analytics | PARTIAL | +widget real-data (existing) | PARTIAL (tanpa report builder) |
| T/C/S | Tenant/Company/Subscription | PARTIAL | +ResolveApiTenant, +email-unique (existing), +test isolasi | PARTIAL (Spatie teams belum aktif) |

**Ringkasan: COMPLETE 4 · PARTIAL 20 · PLANNED 5 · BROKEN 0.**
(BROKEN awal: Payroll TER & CV screening — keduanya diperbaiki, tersisa
keterbatasan jujur yang didokumentasikan.)
