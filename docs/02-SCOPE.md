# 02 — Scope, Constraints & Glossary

## 1. Phase 1 MVP Scope

### MVP Goal (1 kalimat)
> Owner perusahaan UKM 20–200 karyawan bisa **onboard sendiri**, **run payroll bulanan yang taat PPh 21 TER + BPJS dalam <30 menit**, dan karyawan bisa **absen + cuti + lihat slip dari mobile/WA**.

### Modul In-Scope MVP (Phase 1, 12 modul)
| # | Modul | Justifikasi |
|---|---|---|
| M01 | Employee Database | Foundation, mandatory |
| M02 | Organization & Org Chart | Foundation, mandatory |
| M03 | Contract & Document | Compliance dasar |
| M05 | Approval Workflow Engine | Backbone semua request |
| M07 | Employee Self-Service (ESS) | UX karyawan |
| M09 | Shift & Schedule | Prasyarat attendance |
| M10 | Attendance (GPS+selfie) | Killer feature |
| M11 | Overtime | Sambungan ke payroll |
| M12 | Leave Management | Sambungan ke payroll |
| M13 | Holiday Calendar | Auto-load Indonesia |
| M14 | Payroll Engine | **Money-maker** |
| M15 | PPh 21 TER | Compliance mandatory |
| M16 | BPJS Kes+Naker | Compliance mandatory |

### Modul Out-of-Scope MVP (Phase 2+)
M04 (HR Letter), M06 (Announcement), M08 (E-sign), M17 (Bank disburse), M18 (Reimburse), M19 (ATS), M20 (Onboarding), M21 (OKR), M22 (Appraisal), M23 (Competency), M24 (LMS), M25 (Asset), M26 (Travel/SPPD), M27 (Loan), M28 (Exit), M29 (Analytics)

### Differentiator In-Scope MVP
- ✅ Multi-tenant dari Day 1
- ✅ Mobile app Flutter (clock-in + cuti + slip)
- ✅ Mock GPS detection
- ✅ Multi-company / multi-entity (group)
- ✅ Audit log per field
- ✅ License v3 pairing

### Differentiator Out-of-Scope MVP (Phase 5+)
- ⏳ WhatsApp bot full-fitur
- ⏳ Face recognition
- ⏳ AI CV screening
- ⏳ AI narrative insight + predictive turnover
- ⏳ OCR receipt
- ⏳ Bank account validation
- ⏳ Direct submit DJP/SIPP API
- ⏳ E-sign BSrE/Privy
- ⏳ Koperasi simpan-pinjam

## 2. Non-Functional Requirements (NFR)

### Performance
- P95 page load <2s pada koneksi 4G Indonesia
- Payroll batch 500 karyawan <5 menit
- Attendance check-in response <1s
- Mobile app cold start <3s

### Scalability
- Single tenant: support 5.000 karyawan tanpa degradasi
- Multi-tenant: 10.000 tenant aktif per shard
- Horizontal scale via stateless app + read-replica DB

### Availability
- SLA 99.9% uptime (downtime <44 menit/bulan)
- RTO ≤4 jam, RPO ≤15 menit
- Hot standby DB
- Payroll-day (tanggal 1, 25, 28, 30) priority pengamanan

### Security
- **Encryption at rest**: PII, payroll, API key, NIK, salary (AES-256)
- **Encryption in transit**: TLS 1.3 only
- **Auth**: 2FA mandatory admin, optional karyawan
- **Session**: JWT + refresh token, timeout configurable
- **Audit log**: immutable, append-only, retention ≥7 tahun (UU PDP)
- **PII access log**: setiap akses data karyawan dicatat
- **Pen-test**: annual third-party
- **ISO 27001-ready** architecture (cert tahun 2)

### Compliance Indonesia
- **PPh 21**: TER 2024 + DTP scenarios
- **BPJS**: Kesehatan + Ketenagakerjaan (JHT/JP/JKK/JKM) cap & rules terkini
- **UU Cipta Kerja**: pesangon calculator, PKWT/PKWTT, outsourcing
- **Kepmenaker 102/2004**: overtime calculation
- **UU PDP**: consent tracking, data export request, deletion request, breach notification
- **Coretax-ready**: API integration roadmap

### Localization
- Bahasa Indonesia (default) + English
- Currency: IDR (default) + USD/SGD/MYR (multi-currency for expat)
- Timezone: Asia/Jakarta (default) + Asia/Makassar + Asia/Jayapura
- Calendar: Gregorian + Hijriah (untuk THR & cuti agama)
- Number format: 1.000.000,50 (Indonesia)

### Usability
- WCAG 2.1 AA compliance
- Mobile-first responsive web
- Offline-mode untuk attendance + dokumen download
- Bantuan kontekstual (tooltip + help articles)
- Onboarding wizard untuk first-time admin

### Maintainability
- Code coverage ≥70% backend, ≥50% mobile
- Linter: PHPStan level 8, Pint, ESLint strict
- CI/CD: pipeline test + build + deploy automated
- Docs: PHPDoc, API spec OpenAPI 3.0

### Reliability
- Idempotent API (payroll re-run, attendance double-tap)
- Background queue: retry 3x exponential, dead-letter queue
- Backup: harian incremental, mingguan full, retention 90 hari
- Disaster recovery drill: 6-bulanan

## 3. Assumptions

| # | Asumsi | Implikasi jika salah |
|---|---|---|
| A01 | Mayoritas user UKM punya akses smartphone Android | Kita prioritize mobile + WA bot |
| A02 | Sinyal data di Indonesia cukup untuk real-time GPS | Wajib offline mode + sync |
| A03 | Owner mau bawa key provider sendiri (BYOK) | Dynamic adapter wajib |
| A04 | Compliance Indonesia stabil-stable (tidak revolusi major) | Auto-update engine + tim compliance |
| A05 | Pesaing tidak banting harga >50% | Differentiate by feature, not pricing war |
| A06 | License v3 (whitelabel.co.id) jadi proteksi cukup | Re-evaluate kalau ada bypass massal |
| A07 | Laravel ekosistem tetap relevan 5-tahun ke depan | Modular code untuk migrate kalau perlu |
| A08 | Flutter mobile UX competitive vs native | Re-evaluate per 18 bulan |

## 4. Constraints

### Technical Constraints
- **Stack lock**: Laravel 13 + MySQL 8 + Filament 4 + Flutter (per user preference)
- **No hardcoded providers**: semua integrasi via DB config (per CLAUDE.md §2)
- **License v3**: drop-in dari project marketplace standar
- **MySQL**: tidak Postgres (per stack-lock)
- **No SaaS lock-in** untuk vendor (Stripe, Vercel, Supabase, dll.) — kalau pakai harus replaceable

### Business Constraints
- **Bootstrap budget** — no VC funding tahap 1
- **Tim kecil** — 1-2 engineer + 1 founder operator
- **Target market Indonesia** — bahasa, compliance, currency priority
- **License-once mandatory** — minimal ada tier yang ga subscribe forever

### Regulatory Constraints
- Data residency: tenant Indonesia → server Indonesia (UU PDP)
- Audit trail PII retention ≥7 tahun
- Hak akses + hak hapus data karyawan harus terimplementasi

### Time Constraints
- MVP: 4 minggu (Phase 1)
- First paying customer: 8 minggu (Phase 3)
- 100 paying customer: 6 bulan
- Break-even: 12 bulan

## 5. Out of Scope (Permanent / Long-term)

- ❌ **Native iOS Swift / Android Kotlin** — Flutter cukup
- ❌ **ERP / Accounting penuh** — kita HRIS, accounting integrate via webhook
- ❌ **CRM** — bukan domain kita
- ❌ **Office productivity** (chat, doc collab) — bukan domain
- ❌ **Recruitment marketplace** (career portal multi-perusahaan) — internal ATS saja
- ❌ **Banking license / payment provider sendiri** — selalu via Flip/Xendit/bank adapter
- ❌ **Custom code per tenant** — semua via config; kalau butuh custom, jadi feature umum

## 6. Glossary

### HR Terms (Indonesia)
- **BPJS Kesehatan** — Badan Penyelenggara Jaminan Sosial bidang Kesehatan (4% employer + 1% employee, cap upah Rp 12 juta)
- **BPJS Ketenagakerjaan** — terdiri JHT (5.7%), JP (3%), JKK (0.24–1.74%), JKM (0.3%)
- **PPh 21** — Pajak Penghasilan pasal 21 karyawan
- **PPh 26** — Pajak Penghasilan pasal 26 untuk WNA
- **TER 2024** — Tarif Efektif Rata-rata (PMK 168/2023), berlaku 2024+
- **PTKP** — Penghasilan Tidak Kena Pajak (TK/0, K/0, K/1, K/2, K/3, dst.)
- **Form 1721-A1** — bukti potong PPh 21 tahunan
- **Form 1721-I** — laporan PPh 21 bulanan
- **e-Bupot** — elektronik bukti potong (sistem DJP)
- **DJP Coretax** — sistem inti pajak baru DJP (live 2026)
- **SIPP** — Sistem Informasi Pelayanan Peserta (BPJS Ketenagakerjaan)
- **PKWT** — Perjanjian Kerja Waktu Tertentu (kontrak)
- **PKWTT** — Perjanjian Kerja Waktu Tidak Tertentu (tetap)
- **THR** — Tunjangan Hari Raya (wajib H-7 lebaran/natal)
- **UMK / UMP** — Upah Minimum Kabupaten/Provinsi
- **Pesangon** — severance UU CK
- **UU CK** — UU Cipta Kerja 11/2020 (sekarang Perppu 2/2022, UU 6/2023)
- **Kepmenaker 102/2004** — Keputusan Menteri Tenaga Kerja tentang lembur
- **UU PDP** — UU 27/2022 Perlindungan Data Pribadi
- **SP1/SP2/SP3** — Surat Peringatan tingkat 1/2/3
- **SPPD** — Surat Perintah Perjalanan Dinas
- **Paklaring** — surat keterangan kerja (untuk yg sudah resign)

### Product Terms
- **Tenant** — satu perusahaan client (multi-tenant: banyak tenant dalam 1 deployment)
- **Entity / Company** — anak perusahaan dalam group; tenant bisa punya banyak entity
- **Branch** — cabang dalam 1 entity (multi-lokasi)
- **Cost Center** — pusat biaya, biasanya = departemen tapi bisa beda
- **Approval Chain** — rantai persetujuan (manager → manager-of-manager → HRD → CFO)
- **ESS** — Employee Self-Service portal
- **Shift Pattern** — pola jadwal (e.g., 6-1 = 6 hari kerja 1 libur)
- **Roster** — jadwal aktual karyawan vs shift pattern
- **Cycle (payroll)** — periode hitung gaji (bulanan/mingguan)
- **Cut-off Date** — tanggal batas hitung (e.g., 21st-20th, atau 1st-end)

### Tech Terms
- **BYOK** — Bring Your Own Keys (provider API)
- **Multi-tenant** — 1 deployment serve banyak tenant via tenant_id isolation
- **Soft delete** — record di-flag deleted_at, bukan dihapus fisik
- **Audit log** — catat semua perubahan data (siapa, kapan, apa, before/after)
- **Idempotent** — operasi sama berkali-kali = hasil sama (untuk retry safety)
- **Dead-letter queue** — antrian job yang gagal setelah retry max
- **Geofence** — radius GPS yang valid untuk clock-in
- **Anti-spoofing** — deteksi foto/video di depan kamera (bukan wajah asli)

## 7. Decision Log

Decision yang sudah dipastikan, untuk mencegah re-debate:

| # | Decision | Date | Rationale |
|---|---|---|---|
| D01 | Stack: Laravel 13 + MySQL + Filament 4 + Flutter | 2026-05-21 | User preference + sudah dipakai di project lain |
| D02 | Multi-tenant dari Day 1 | 2026-05-21 | Avoid migrasi data nanti |
| D03 | License v3 wajib | 2026-05-21 | Whitelabel sales channel |
| D04 | No hardcoded providers (LLM, bank, SMS, WA) | 2026-05-21 | Global rule CLAUDE.md §2 |
| D05 | pSEO wajib | 2026-05-21 | Global rule CLAUDE.md §1 |
| D06 | Dual model SaaS + License-once | 2026-05-21 | Capture self-host enterprise |
| D07 | Mobile via Flutter (bukan native) | 2026-05-21 | Single codebase, cukup untuk MVP |
| D08 | WhatsApp bot via WhatsApp Business API + adapter dinamis | 2026-05-21 | Differentiator + no vendor lock |
| D09 | Payroll engine pakai formula builder (excel-like) | 2026-05-21 | Fleksibel untuk berbagai industri |
| D10 | Auth: Sanctum (API) + Filament built-in (admin) | 2026-05-21 | Standard Laravel |
| D11 | Queue: Redis + Laravel Horizon | 2026-05-21 | Standard, well-supported |
| D12 | Storage: S3-compatible (configurable provider) | 2026-05-21 | No vendor lock |
| D13 | PPh 21: TER 2024 default, opsi gross/nett/gross-up | 2026-05-21 | Compliance |

## 8. Open Questions (Need User Input)

Pertanyaan yang masih perlu konfirmasi sebelum/selama Phase 1:

| # | Question | Default if no answer |
|---|---|---|
| Q01 | Brand name produk? | "hris" codename |
| Q02 | Primary domain? | hris.test untuk dev |
| Q03 | Tim engineering size? | Asumsi 1-2 dev |
| Q04 | Mau pSEO route mana yang launch dulu? | 3 jenis priorities (best, compare, alternatives) |
| Q05 | Target industri pertama? | Generic, F&B + retail prioritized in marketing |
| Q06 | Bank apa saja yang prioritas integrate? | BCA, Mandiri, BNI, BRI, CIMB |
| Q07 | WA gateway: Twilio, Vonage, Wati, Maytapi, atau Fonnte? | Adapter dynamic — owner pilih |
| Q08 | Hosting awal: VPS Indonesia (Niagahoster/Biznet) atau cloud (DO/AWS)? | VPS Indonesia (UU PDP residency) |
| Q09 | License v3 marketplace public, atau invite-only? | Public di whitelabel.co.id |
| Q10 | Demo data: bahasa Indonesia atau English? | Indonesia (target pasar utama) |

> **Pendekatan:** lanjutkan dengan default; user bisa override kapan saja.
