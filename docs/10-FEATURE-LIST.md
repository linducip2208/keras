# 10 — Feature List (Checklist Mode)

> Daftar fitur **flat** semua modul untuk review cepat sebelum scaffold. Total **300+ fitur** terbagi di 29 modul.
>
> Format: ✅ = MVP Phase 1 · 🟡 = Phase 2-4 · 🔵 = Phase 5+ (differentiator/AI/WA) · ⚪ = Future/Year 2
>
> Untuk komparasi vs competitor → lihat [`../FEATURES.md`](../FEATURES.md)
> Untuk detail per modul → lihat [`04-MODULES.md`](04-MODULES.md)

---

## CORE HR

### M01 Employee Database
- ✅ Data pribadi (NIK 16-digit, NPWP, alamat KTP + domisili)
- ✅ Tempat & tanggal lahir, agama, status pernikahan, jenis kelamin
- ✅ PTKP status (TK/0, TK/1, ..., K/3)
- ✅ Data keluarga (suami/istri + 3 anak BPJS dependent + ortu)
- ✅ Kontak darurat (multi)
- ✅ Riwayat pendidikan (SD–S3)
- ✅ Sertifikasi + skill matrix
- ✅ Bank account multiple (split disburse)
- ✅ Dokumen upload (KTP, NPWP, BPJS, ijazah, sertifikat)
- ✅ Expiry alert (KTP, paspor, SIM, BPJS, kontrak)
- ✅ Custom field per perusahaan
- ✅ QR code badge ID auto-generate
- ✅ Bulk import Excel + validation
- ✅ Foreign worker support (visa, KITAS, IMTA)
- ✅ Photo + signature scan
- ✅ Audit log per field (siapa ubah apa kapan)

### M02 Organization & Org Chart
- ✅ Multi-company / multi-entity (group)
- ✅ Branch / lokasi kerja (geofence default, jam kerja)
- ✅ Department + cost center
- ✅ Division / sub-divisi
- ✅ Job title + job level
- ✅ Position (jabatan) — kombinasi unik
- ✅ Reporting line (manager + dotted line)
- ✅ Org chart visual drag-drop
- ✅ Position history (mutasi/promosi/demosi)
- ✅ Headcount budget per dept

### M03 Contract & Document
- ✅ PKWT, PKWTT, probation, magang, harian, freelance, outsource
- ✅ Probation tracking + auto reminder end
- ✅ Contract expiry alert (60/30/7 hari)
- ✅ Auto-renewal workflow
- ✅ Contract template builder + variable merge
- ✅ Nomor kontrak auto-increment
- ✅ PDF generate
- 🟡 E-signature integration (BSrE/Privy)
- 🔵 WhatsApp delivery kontrak + read receipt
- ✅ Document repository (semua dokumen)
- ✅ Document expiry alert generik

### M04 HR Letter Generator
- 🟡 SK pengangkatan / penetapan
- 🟡 SK mutasi / promosi / demosi
- 🟡 Surat paklaring / referensi
- 🟡 SP1 / SP2 / SP3 (surat peringatan)
- 🟡 SPPD (surat perjalanan dinas)
- 🟡 Template builder rich text + variable
- 🟡 Nomor surat auto-increment per kategori (format custom)
- 🟡 PDF generate
- 🟡 Bulk generate (200 SK sekaligus)
- 🟡 Watermark dynamic (DRAFT/CONFIDENTIAL)
- 🟡 Arsip otomatis ke folder dokumen karyawan

### M05 Approval Workflow Engine
- ✅ Multi-level sequential
- ✅ Parallel approval (semua harus approve)
- ✅ Conditional routing (nominal/dept/role)
- ✅ Delegation saat approver cuti
- 🟡 Auto-escalation timeout
- ✅ Comment + attachment per step
- ✅ Approval history audit
- ✅ Approval via mobile push
- 🟡 Approval via email link
- 🔵 Approval via WhatsApp reply

### M06 Announcement & Broadcast
- 🟡 Broadcast all / per dept / per branch / per role / segment
- 🟡 Rich text + image + attachment
- 🟡 Schedule publish + expiry
- 🟡 Pin priority (sticky)
- 🟡 Read receipt + acknowledgment tracking
- 🟡 Polling / quick survey
- 🟡 Birthday + work anniversary auto-broadcast
- 🟡 Welcome new hire auto-broadcast
- 🟡 Hari raya auto-broadcast

### M07 Employee Self-Service (ESS)
- ✅ Dashboard: shift hari ini, saldo cuti, slip terbaru
- ✅ View slip gaji + tax form
- ✅ View kontrak aktif + download
- ✅ Request cuti / izin / sakit
- ✅ Request lembur
- 🟡 Request reimbursement
- 🟡 Request shift swap
- ✅ View attendance log + correction request
- ✅ Update profile (alamat, contact, bank — dengan HR approval)
- ✅ Family info update
- 🟡 Download HR letter sendiri
- ✅ View team calendar
- ✅ View org chart
- 🟡 Birthday list bulan ini
- ✅ Mobile app iOS + Android
- ✅ Web portal responsive
- 🔵 WhatsApp bot full-fitur

### M08 Digital Signature
- 🟡 Adapter BSrE (BSSN) — PSrE Indonesia legal
- 🟡 Adapter Privy ID
- 🟡 Adapter DigiSign / TekenAja / generic PKI
- 🟡 Single + multi-party signature
- 🟡 OTP via SMS/email untuk verify signer
- 🟡 Audit trail (IP, device, timestamp)
- 🟡 Sertifikat digital embed di PDF
- 🟡 Bulk sign (HR sign 100 surat sekaligus)

---

## TIME, ATTENDANCE & LEAVE

### M09 Shift & Schedule
- ✅ Fixed shift (9-5)
- ✅ Rotating shift (pagi/siang/malam)
- ✅ Split shift (potong istirahat panjang)
- ✅ Flexible shift / no shift (WFA)
- ✅ On-call shift
- ✅ Shift pattern builder (6-1, 5-2, 4-3)
- ✅ Working hours + break time per shift
- ✅ Schedule template per dept/role
- 🟡 Auto-roster generator
- ✅ Manual roster edit
- 🟡 Shift swap antar karyawan (approval manager)
- ✅ Calendar view weekly/monthly
- 🟡 Conflict detection (overlap, max 40 jam/minggu UU CK)

### M10 Attendance (Clock-in/Out)
- ✅ GPS check-in + geofence radius
- ✅ Selfie photo + timestamp watermark
- 🔵 Face recognition + anti-spoofing
- 🟡 Fingerprint device SDK (ZKTeco, Suprema)
- 🟡 QR code scan (kios mode)
- ✅ Multi-location radius
- ✅ WFH / WFA mode
- ✅ Real-time live tracking dashboard
- ✅ Mock GPS / fake location detection
- ✅ Offline mode + auto-sync
- ✅ Late / early-leave / absent auto-flag
- ✅ Auto-deduct salary on absent
- ✅ Attendance correction request workflow
- ✅ Bulk approval correction
- ✅ Daily attendance summary

### M11 Overtime
- ✅ Request lembur pre/post
- ✅ Kepmenaker 102/2004 auto-calculate
- ✅ Tarif weekday vs weekend/libur
- ✅ Cap lembur per bulan/minggu
- 🟡 Lembur ditukar cuti pengganti (CL)
- 🟡 Budget lembur per dept (warning over)
- ✅ Auto-flow ke payroll
- ✅ Bukti foto/dokumen pekerjaan lembur

### M12 Leave Management
- ✅ Cuti tahunan (saldo per user)
- ✅ Cuti sakit (dengan/tanpa surdok)
- ✅ Cuti melahirkan + ayah
- ✅ Cuti menikah / khusus / besar
- ✅ Unpaid leave auto-deduct payroll
- ✅ Custom leave type per perusahaan
- ✅ Carry-over saldo tahun depan + expiry
- ✅ Half-day leave
- ✅ Cuti bersama auto-deduct
- 🔵 Cuti ulang tahun (perk differentiator)
- 🟡 Leave conflict warning (overlap team)
- ✅ Calendar view team

### M13 Holiday Calendar
- ✅ Pre-loaded tanggal merah Indonesia (auto-update)
- ✅ Cuti bersama dari SKB 3 Menteri
- ✅ Custom holiday per company/branch
- 🟡 Religion-specific holiday (Idul Adha, Natal, Nyepi, Waisak, Imlek)
- 🟡 Calendar export (iCal / Google)

---

## PAYROLL & COMPENSATION

### M14 Payroll Engine
- ✅ Komponen gaji custom unlimited (earning + deduction + benefit)
- ✅ Formula builder excel-like
- ✅ Earning: gapok, tunjangan tetap/variable, komisi, bonus, THR
- ✅ Deduction: BPJS karyawan, PPh 21, pinjaman, unpaid leave, denda
- 🟡 Benefit non-tunai: asuransi tambahan, voucher
- ✅ Pro-rata mid-month join/resign
- ✅ Multi-period (bulanan/mingguan/dua-mingguan/harian)
- ✅ Off-cycle payroll (bonus, THR, koreksi)
- ✅ THR generator (1x gaji + tunjangan tetap, prorate)
- 🟡 Komisi penjualan / sales incentive
- ✅ Variable allowance from attendance (uang makan per hadir)
- 🟡 Multi-currency untuk expat
- ✅ Payslip PDF generate
- ✅ Email payslip + password lock (NIK/DOB)
- 🔵 WhatsApp delivery payslip
- ✅ Payroll lock + approval (multi-level)
- ✅ Audit trail payroll changes
- ✅ Bulk-edit support
- ✅ Payroll re-run (idempotent)
- ✅ Background queue (5 menit untuk 500 karyawan)

### M15 Tax PPh 21 / 26
- ✅ PPh 21 TER 2024 (PMK 168/2023)
- ✅ Metode Gross / Nett / Gross-up
- ✅ PPh 21 final (pesangon, severance, JHT)
- 🟡 PPh 26 untuk WNA non-resident
- ✅ Status PTKP (TK/0 sampai K/3)
- ✅ Form 1721-A1 generator (PDF)
- ✅ Form 1721-I (bulanan)
- 🟡 e-Bupot integration / export format DJP
- 🔵 Direct submit DJP Coretax API (saat live)
- ✅ Auto-update saat aturan baru
- 🟡 Tax simulation calculator
- 🟡 DTP (Ditanggung Pemerintah) scenarios

### M16 BPJS Kesehatan & Ketenagakerjaan
- ✅ BPJS Kesehatan 4% employer + 1% employee, cap 12jt
- ✅ JHT 3.7% employer + 2% employee
- ✅ JP 2% employer + 1% employee, cap 10.042jt 2026
- ✅ JKK 0.24%–1.74% (per risk level)
- ✅ JKM 0.3% employer
- ✅ Dependents (suami/istri + 3 anak)
- ✅ Cap upah BPJS (auto-update)
- ✅ Auto-generate file SIPP (XLSX upload-ready)
- 🔵 Direct submit SIPP API (saat tersedia)
- 🔵 Klaim JKK/JKM workflow
- ✅ Iuran berbeda per cabang/UMK (multi-cabang)
- 🟡 Voluntary BPJS untuk freelancer

### M17 Bank Disbursement
- 🟡 Batch file BCA (KlikBCA Bisnis CSV)
- 🟡 Batch file Mandiri (Cash Management TXT)
- 🟡 Batch file BNI (BNI Direct XLSX)
- 🟡 Batch file BRI (CMS CSV)
- 🟡 Batch file CIMB (BizChannel CSV)
- 🟡 Batch file Permata, Danamon
- 🟡 Instant disbursement (Flip / Xendit / Doku / OY API)
- 🔵 Bank account validation (cek nomor rekening valid)
- 🟡 Re-run failed transfer + retry
- 🟡 Disbursement history + reconciliation
- 🟡 Multi-bank split (70% bank A, 30% bank B)
- 🟡 Approval finance sebelum execute
- 🟡 Confirmation upload (slip transfer bukti)

### M18 Reimbursement & Expense
- 🟡 Kategori custom (transport, meals, medical, entertainment)
- 🟡 Policy limit per kategori per role
- 🟡 Budget tracking real-time per dept
- 🟡 Upload bukti foto/PDF
- 🔵 OCR receipt (auto-extract nominal & vendor & date)
- 🟡 Flexible approval rules
- 🟡 Auto-flow ke payroll atau separate disbursement
- 🟡 Multi-currency reimbursement
- 🟡 Per-diem (uang harian dinas)
- ⚪ Corporate card reconciliation

---

## TALENT & PERFORMANCE

### M19 Recruitment / ATS
- 🟡 Career page publik (subdomain `karir.{tenant}.hris.app`)
- 🟡 Job posting dengan rich description
- 🟡 Share job ke channel (LinkedIn, Jobstreet, Glints)
- 🟡 LinkedIn integration import lowongan
- 🔵 AI CV screening + match score
- 🟡 Multi-filter (skill, lokasi, exp, salary)
- 🟡 Pipeline kanban drag-drop
- 🟡 Interview scheduler + calendar invite + Google Meet
- 🟡 Interview feedback form + scorecard
- 🔵 1-click WhatsApp/Email blast kandidat
- 🟡 Talent pool / candidate database
- 🟡 Offer letter generator + e-sign
- 🟡 Referral program tracking
- 🟡 Auto-reject email template
- ⚪ Background check integration

### M20 Onboarding & Probation
- 🟡 Onboarding checklist per role (template)
- 🟡 Document collection (KTP, ijazah, NPWP, BPJS)
- 🟡 Asset assignment workflow
- 🟡 Welcome email + intro slide
- 🟡 Buddy assignment (mentor 90 hari)
- 🟡 Probation 30/60/90-day review
- 🟡 Auto-end probation reminder
- 🟡 Onboarding survey (eNPS pertama)
- 🟡 E-learning mandatory link (LMS)

### M21 OKR & KPI
- 🟡 Cascading goals unlimited level
- 🟡 Company → dept → team → individual
- 🟡 Quarterly atau annual cycle
- 🟡 Key Result dengan target measurable
- 🟡 KPI weight & scoring
- 🟡 Progress update (manual % atau auto)
- 🟡 Check-in mingguan / bulanan
- 🟡 Alignment view (siapa support siapa)
- 🟡 Reminder in-app + email
- 🟡 OKR retrospective tool
- 🟡 Public vs private OKR

### M22 Performance Appraisal 360°
- 🟡 360° review (self, peer, subordinate, manager)
- 🟡 Custom appraisal form builder
- 🟡 9-box matrix (performance × potential)
- 🟡 Calibration meeting tool
- 🟡 Cycle scheduler (annual/quarterly)
- 🟡 Goal-linked appraisal (link ke OKR)
- 🟡 Anonymous peer feedback
- 🟡 Continuous feedback (kudos anytime)
- 🔵 AI summary 360 feedback
- 🟡 Compa-ratio analysis (salary vs market)

### M23 Competency & Succession
- 🟡 Competency framework
- 🟡 Skill matrix per role
- 🟡 Skill gap analysis
- 🟡 Career path visualization
- 🟡 Succession planning (talent pool per critical position)
- 🟡 Promotion-ready dashboard
- 🟡 IDP (Individual Development Plan)

### M24 Learning & Development (LMS)
- 🟡 Course catalog (video, PDF, SCORM, link)
- 🟡 Quiz & assessment
- 🟡 Certification + expiry tracking
- 🟡 Training hours tracking
- 🟡 External training expense link
- 🟡 Mandatory training auto-assign per role
- 🟡 Course rating + feedback
- 🔵 AI-generated quiz dari materi
- 🟡 Learning path (course bundle)
- 🟡 Compliance reporting

---

## OPERATIONS & ANALYTICS

### M25 Asset Management
- 🟡 Asset register (laptop, hp, kendaraan, ID card)
- 🟡 Assignment to employee + handover form
- 🟡 Return on offboarding (auto-checklist)
- 🟡 Maintenance schedule
- 🟡 Depreciation tracking
- 🟡 Service record (servis, repair)
- 🟡 Asset request (karyawan butuh laptop baru)

### M26 Travel / Business Trip (SPPD)
- 🟡 Trip request workflow
- 🟡 Cash advance (uang muka)
- 🟡 Per-diem auto-calc per kota / per role
- 🟡 Settlement & expense report
- 🟡 SPPD letter auto-generate
- ⚪ Travel agent integration (Traveloka Biz)
- 🟡 Hotel + flight booking history
- 🟡 Travel policy enforcement (kelas pesawat, hotel rate max)
- 🟡 Mileage tracking kendaraan pribadi

### M27 Loan / Koperasi Karyawan
- 🟡 Cash advance request (≤1 bulan gaji)
- 🟡 Installment loan + interest
- 🟡 Auto-deduct payroll per cicilan
- 🟡 Loan policy (max % gaji, max tenor, min tenure)
- 🔵 Koperasi simpan-pinjam mode (differentiator)
- 🟡 Loan history per karyawan
- 🟡 Outstanding balance dashboard
- 🟡 Default management (resign masih ada hutang)

### M28 Exit Management
- 🟡 Resignation request workflow
- 🟡 Clearance checklist (asset return, finance, knowledge handover)
- 🟡 Exit interview form (anonymous opsi)
- 🟡 Final payment (pesangon, sisa cuti, THR prorate)
- 🟡 Pesangon UU CK calculator
- 🟡 Paklaring auto-generate
- 🔵 Access revocation checklist (email, GitHub, Slack, SaaS)
- 🔵 Boomerang tag (rehirable / no-rehire)
- 🟡 Last working day reminder ke team
- 🟡 Reason analysis (turnover insight)

### M29 Analytics & Reporting
- 🟡 Attendance dashboard (kehadiran, terlambat, absen, lembur)
- 🟡 Payroll cost by dept / cost center / time
- 🟡 Headcount + turnover rate + retention
- 🟡 Performance distribution (9-box, rating)
- 🟡 Recruitment funnel + time-to-hire
- 🟡 Leave usage + balance
- 🟡 Training completion + cost
- 🟡 Custom report builder (drag-drop)
- 🟡 Scheduled report email (daily, weekly, monthly)
- 🟡 Export Excel + PDF + CSV
- 🔵 AI insight & narrative summary
- 🔵 Predictive analytics (turnover risk)
- ⚪ Compensation benchmark vs market
- 🟡 BI integration (Metabase, Looker, Power BI)

---

## CROSS-CUTTING (Backbone)

### CX01 Notification Service
- ✅ In-app notification
- ✅ Push (FCM mobile)
- 🟡 Email
- 🔵 WhatsApp
- 🟡 SMS
- ✅ Tenant + user preferensi per event type

### CX02 Audit Log & Compliance
- ✅ Activity log (create/update/delete event)
- ✅ PII access log (siapa lihat data siapa kapan)
- ✅ Login event log
- ✅ Retention 7 tahun (UU PDP)
- 🟡 Right to access (data export request)
- 🟡 Right to deletion (deletion request workflow)
- 🟡 Right to portability (JSON export)
- 🟡 Breach notification process

### CX03 File Storage
- ✅ S3-compatible adapter (MinIO, Wasabi, R2, AWS S3)
- ✅ Encryption at rest
- ✅ Signed URL temporary
- ✅ Versioning

### CX04 Webhook (Outbound)
- 🟡 Tenant register URL per event
- 🟡 HMAC-SHA256 signature
- 🟡 Retry 5x exponential
- 🟡 Dead-letter queue

### CX05 API Gateway
- ✅ REST API v1
- ✅ Sanctum token auth
- ✅ Rate limit per token
- 🟡 GraphQL endpoint (opsional)
- 🟡 OpenAPI 3.0 spec auto-generate

### CX06 License Gate
- ✅ License v3 drop-in (whitelabel.co.id kit)
- ✅ Pairing wizard `/__pair`
- ✅ Trial mode 14 hari watermark
- ✅ SaaS mode (online validation)
- ✅ Self-host mode (offline-tolerant 30 hari)
- ✅ Whitelabel reseller token

### CX07 Multi-Tenant
- ✅ Row-level tenant_id (default)
- 🟡 DB-per-tenant option (enterprise)
- ✅ Subdomain routing
- 🟡 Custom domain
- ✅ Tenant onboarding self-serve
- ✅ Trial → paid conversion
- 🟡 Tenant suspend / data export / data deletion

### CX08 Super-Admin Panel
- ✅ Tenant management
- 🟡 Billing & subscription
- 🟡 Support ticket integration
- 🟡 System health dashboard
- 🟡 License audit log

---

## INTEGRATIONS (Dynamic, BYOK)

### Bank Disbursement
- 🟡 Batch file BCA / Mandiri / BNI / BRI / CIMB / Permata / Danamon
- 🟡 Instant: Flip / Xendit / Doku / OY

### E-Signature
- 🟡 BSrE (BSSN) / Privy / DigiSign / TekenAja / generic PKI

### WhatsApp
- 🔵 Meta Official / 360dialog / Wati / Fonnte / Maytapi / Chatwoot

### LLM AI
- 🔵 OpenAI-compatible (OpenAI, DeepSeek, Groq, Mistral, Together, OpenRouter, Cerebras, Ollama, LM Studio, vLLM)
- 🔵 Anthropic / Gemini / Azure OpenAI

### OCR / Vision
- 🔵 Vision LLM / AWS Textract / Mindee / Nanonets

### Mail
- 🟡 SMTP / Mailgun / SendGrid / SES / Postmark / Brevo / Resend

### SMS
- 🟡 Twilio / Vonage / Zenziva / JatisMobile / Wavecell / SMPP

### Push
- ✅ FCM v1 / APNs / OneSignal

### Storage
- ✅ S3 / MinIO / Wasabi / B2 / R2 / GCS / Azure Blob

### Accounting (Phase 6+)
- ⚪ Accurate / Jurnal / Xero / QuickBooks / Odoo / webhook

### Government
- 🔵 DJP Coretax (saat live 2026)
- 🔵 BPJSTK SIPP direct submit

### Identity / KYC
- 🔵 Bank account validation (Flip / Xendit / BRI API)
- ⚪ Background check partner

### Calendar
- 🟡 Google Calendar / Outlook / CalDAV / iCal

### Job Board
- 🟡 LinkedIn / Jobstreet / Glints / generic webhook

### Travel (Phase 6+)
- ⚪ Traveloka Biz / Agoda Business / Pegipegi

### Payment (SaaS billing — operator-side)
- ✅ Redirect (Midtrans/Xendit/Doku/Flip)
- 🟡 Embed (Stripe Elements)
- 🟡 QRIS
- ✅ Bank transfer manual

---

## MARKETING & PUBLIC

### Landing Page (`/`)
- 🔵 Hero + 2 CTA
- 🔵 Trust strip / use case strip
- 🔵 Problem-solution (5 before/after pairs)
- 🔵 10 feature sections alternating + real screenshots (Playwright)
- 🔵 Screenshot gallery 6-9 halaman
- 🔵 4 use case persona (industry-specific)
- 🔵 Demo accounts table dengan login link
- 🔵 Pricing 4 tier + license-once
- 🔵 Final CTA dark section
- 🔵 Footer lengkap (Produk, Solusi, Resources, Perusahaan, Legal)

### pSEO Routes
- 🔵 `/best-aplikasi-hris-{year}` (annual)
- 🔵 `/best-aplikasi-hris-untuk-{industri}` (10 industri)
- 🔵 `/compare/{a}-vs-{b}` (7 competitor)
- 🔵 `/alternatives-to-{competitor}`
- 🔵 `/aplikasi-hris-{kota}` (40 kota)
- 🔵 `/aplikasi-payroll-{kota}` (40 kota)
- 🔵 `/aplikasi-absensi-{industri}` (10 industri)
- Total: ~250+ unique pages, semua dengan JSON-LD + 300+ kata + sitemap

### Public Web Other
- 🔵 Pricing page
- 🔵 Demo request form
- ⚪ Blog
- ⚪ Customer story page
- 🔵 About + contact + privacy + terms
- 🔵 ISO 27001 + UU PDP compliance page

---

## DIFFERENTIATOR (Killer Features — 30 fitur)

### Tier 1 — Killer (10)
- 🔵 WhatsApp Bot Full ESS (clock-in, cuti, slip, approve via WA)
- 🔵 AI Narrative Insight (laporan "narasi" otomatis)
- 🔵 Predictive Turnover Risk (ML)
- 🔵 Compensation Benchmark vs Market
- 🔵 Face Recognition + Anti-spoofing
- ✅ Mock GPS Detection
- ✅ Offline Mode Attendance
- 🔵 OCR Receipt (foto struk → auto-data)
- 🔵 Bank Account Validation
- ✅ Multi-company / Group

### Tier 2 — Power User (10)
- 🔵 Direct submit DJP API
- 🔵 Direct submit SIPP API
- 🔵 Klaim JKK/JKM workflow
- 🟡 E-signature BSrE/Privy integration
- ✅ Audit log per field (compliance ready)
- 🟡 Auto-escalation timeout approval
- ⚪ Travel agent integration (Traveloka Biz)
- 🔵 AI-generated quiz dari materi
- 🔵 Koperasi simpan-pinjam mode
- 🔵 Access revocation checklist (offboarding aman)

### Tier 3 — Delight (10)
- 🔵 Cuti ulang tahun (perk)
- 🟡 Calendar export iCal/Google
- 🟡 Religion-specific holiday
- 🟡 Polling / quick survey
- 🟡 Continuous feedback (kudos anytime)
- 🟡 Birthday + work anniversary auto-broadcast
- 🔵 Boomerang tag (rehirable / no-rehire)
- 🟡 Budget lembur warning per dept
- 🟡 Referral program tracking
- ⚪ Background check integration

---

## SUMMARY COUNT

| Tier | Fitur | %tase |
|---|---|---|
| ✅ MVP Phase 1 | ~115 fitur | 38% |
| 🟡 Phase 2-4 | ~140 fitur | 46% |
| 🔵 Phase 5+ (Differentiator/AI/WA) | ~40 fitur | 13% |
| ⚪ Future / Year 2 | ~8 fitur | 3% |
| **TOTAL** | **~303 fitur** | **100%** |

---

## NEXT ACTION

✅ Review list ini  
✅ Beri tahu fitur mana yang **tambah**, **kurang**, atau **prioritas berubah** (pindahkan ✅↔🟡↔🔵)  
✅ Setelah list ini di-approve → lanjut Phase 1 scaffold (Laravel + 29 module migrations + seeders)
