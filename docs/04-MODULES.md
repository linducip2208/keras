# 04 — Module Breakdown (29 Modules)

> Tiap modul didokumentasi dengan: **Purpose**, **Primary user**, **Key features**, **User stories**, **Dependencies**, **Key entities**, **API endpoints**, **Mobile/WA support**.

## Dependency Graph

```
                ┌─────────────────────────────────────┐
                │  M01 Employee Database              │◄─ foundation
                │  M02 Organization                   │
                └────┬─────────────────────────┬──────┘
                     │                         │
            ┌────────┼────────────┐            │
            │        │            │            │
   ┌────────▼┐ ┌────▼────┐ ┌─────▼─────┐ ┌────▼────┐
   │M03 Ctrct│ │M07 ESS  │ │ M05 Approve│ │M06 Annc │
   │M04 Letter│ │M08 E-Sign│ │   Workflow │ │         │
   └────┬─────┘ └─────────┘ └─────┬──────┘ └─────────┘
        │                          │
        │     ┌────────────────────┴──────────────────┐
        │     │                                        │
   ┌────▼─────▼───────┐                          ┌────▼───────┐
   │ M09 Shift        │                          │ M19 ATS    │
   │ M10 Attendance   │                          │ M20 Onbd   │
   │ M11 Overtime     │                          └────────────┘
   │ M12 Leave        │
   │ M13 Holiday      │
   └────────┬─────────┘
            │
            ▼
   ┌──────────────────┐         ┌──────────────────┐
   │ M14 Payroll      │◄────────│ M18 Reimburse    │
   │ M15 Tax PPh21    │         │ M27 Loan         │
   │ M16 BPJS         │         │ M26 Travel SPPD  │
   │ M17 Bank Disburse│         └──────────────────┘
   └────────┬─────────┘
            │
            ▼
   ┌──────────────────┐
   │ M21 OKR/KPI      │
   │ M22 Appraisal    │
   │ M23 Competency   │
   │ M24 LMS          │
   └──────────────────┘
            │
            ▼
   ┌──────────────────┐
   │ M25 Asset        │
   │ M28 Exit         │
   │ M29 Analytics    │
   └──────────────────┘
```

---

## M01 — Employee Database & Master Data

**Purpose:** Single source of truth seluruh data karyawan dengan validasi NIK/NPWP Indonesia, dokumen, expiry alert, dan custom field per perusahaan.

**Primary user:** HR Admin

**Key features:**
- Data pribadi (NIK 16-digit, NPWP, tempat/tgl lahir, agama, status pernikahan, alamat KTP + domisili, jenis kelamin, gol darah)
- Keluarga (suami/istri + maks 3 anak untuk BPJS dependents, ibu/ayah)
- Kontak darurat
- Riwayat pendidikan (SD–S3) + sertifikasi + skill
- Bank account multiple (default + cadangan, untuk split disburse)
- Dokumen upload (KTP, NPWP, BPJS card, ijazah, sertifikat, surat sehat, dll.)
- Expiry alert (KTP, paspor, SIM, BPJS, kontrak, visa kerja)
- Custom field per perusahaan
- QR code badge ID auto-generate
- Bulk import Excel + validation
- Photo + signature
- Foreign worker support (visa, KITAS, IMTA)

**Workflows:**
- Add employee → personal data → contract (M03) → org placement (M02) → invite to ESS

**Key entities:** `employees`, `employee_families`, `employee_documents`, `employee_educations`, `employee_emergency_contacts`, `employee_bank_accounts`, `employee_skills`, `employee_certifications`, `custom_field_values`

**API:** `/api/v1/employees`, `/api/v1/employees/{id}/documents`, `/api/v1/employees/{id}/families`

**Mobile/WA:** view & edit profile (limited fields); update bank account → triggers HR approval

---

## M02 — Organization & Org Chart

**Purpose:** Struktur perusahaan multi-level dengan support multi-company (group), branch, dept, position, level.

**Primary user:** HR Admin

**Key features:**
- Multi-company / multi-entity (group of companies)
- Branch / lokasi kerja (alamat, koordinat geofence default, jam kerja)
- Department + cost center
- Job title + job level / job grade
- Position (jabatan) — separate dari job title (HR Manager vs Manager-of-HR)
- Reporting line (manager + dotted line manager)
- Position history (siapa pernah pegang jabatan apa kapan)
- Position movement (mutasi/promosi/demosi)
- Org chart visual drag-drop
- Headcount budget per dept

**Workflows:**
- Promotion: employee → new position → save history → update salary structure → letter generator (M04)
- Mutation: employee → new dept/branch → update reporting line

**Key entities:** `companies`, `branches`, `departments`, `divisions`, `job_titles`, `job_levels`, `positions`, `position_history`, `employee_assignments`

**API:** `/api/v1/companies`, `/api/v1/branches`, `/api/v1/departments`, `/api/v1/positions`, `/api/v1/org-chart`

**Mobile/WA:** browse org chart (read-only)

---

## M03 — Contract & Document Management

**Purpose:** Kelola kontrak karyawan PKWT/PKWTT/magang dengan auto-expiry alert dan template builder.

**Primary user:** HR Admin

**Key features:**
- Jenis kontrak: PKWT (kontrak), PKWTT (tetap), Probation, Magang, Harian/Freelance, Outsourcing
- Probation tracking (default 3 bulan, configurable)
- Contract expiry alert (60/30/7 hari sebelum berakhir)
- Auto-renewal workflow
- Contract template builder dengan variable merge (`{nama}`, `{jabatan}`, `{gaji}`, dll.)
- Number auto-increment per kategori
- PDF generate + e-sign (M08) optional
- Document repository (semua dokumen karyawan + perusahaan)
- Document expiry alert generik (untuk SIM, paspor, sertifikasi B3, dll.)
- Version control template

**Workflows:**
- New hire → assign contract template → fill variables → generate PDF → e-sign (opsional) → archive
- Contract expiry T-60 → alert HR + manager → renewal decision → new contract atau exit

**Key entities:** `contracts`, `contract_templates`, `contract_template_variables`, `documents`, `document_categories`, `document_expiry_alerts`

**API:** `/api/v1/contracts`, `/api/v1/contracts/{id}/renew`, `/api/v1/contract-templates`

**Mobile/WA:** view kontrak aktif, download PDF

---

## M04 — HR Letter Generator

**Purpose:** Auto-generate semua jenis surat HR dengan template engine + variable merge + e-sign.

**Primary user:** HR Admin, HR Staff

**Key features:**
- Jenis surat: SK pengangkatan, mutasi, promosi, demosi, SP1/SP2/SP3, paklaring, referensi, SPPD, surat sehat permintaan, surat tugas
- Template builder rich text + variable
- Nomor surat auto-increment per kategori dengan format custom (e.g., `SK/2026/HR/001`)
- PDF generate dengan signature placeholder
- E-sign integration (BSrE/Privy) opsional
- Arsip otomatis ke folder dokumen karyawan (M03)
- Bulk generate (e.g., 200 SK kenaikan gaji sekaligus)
- Watermark dynamic (DRAFT, CONFIDENTIAL)

**Workflows:**
- Karyawan resign → exit (M28) → auto-generate paklaring
- Promotion → M02 position update → auto-generate SK mutasi

**Key entities:** `hr_letters`, `hr_letter_templates`, `hr_letter_numbers` (counter), `hr_letter_variables`

**API:** `/api/v1/letters`, `/api/v1/letters/generate`

**Mobile/WA:** karyawan download letter dirinya sendiri

---

## M05 — Approval Workflow Engine

**Purpose:** Backbone semua request — leave, overtime, reimburse, travel, loan, attendance correction — dengan rule yang fleksibel.

**Primary user:** semua user (sebagai requester, approver, atau admin yang setup)

**Key features:**
- Multi-level sequential approval
- Parallel approval (semua harus approve)
- Conditional routing berdasarkan: nominal, dept, role, employee level, jenis cuti, dll.
- Delegation saat approver cuti
- Auto-escalation timeout (e.g., manager tidak respond 2 hari → naik ke manager-of-manager)
- Approval via mobile push, email link, WhatsApp
- Comment + attachment per step
- Approval history audit trail
- Workflow template (predefined chain) per jenis request

**Workflows:**
- Karyawan submit cuti → cek policy → routing engine → manager → HR (kondisi tertentu) → approved/rejected
- Reimburse > Rp 5jt → manager → HR → CFO (3-level karena nominal)

**Key entities:** `approval_workflows`, `approval_workflow_steps`, `approval_requests`, `approval_request_steps`, `approval_delegations`

**API:** `/api/v1/approvals/inbox`, `/api/v1/approvals/{id}/approve`, `/api/v1/approvals/{id}/reject`

**Mobile/WA:** push notif + approve/reject inline; WhatsApp reply "OK" untuk approve

---

## M06 — Announcement & Broadcast

**Purpose:** Komunikasi internal HR ke seluruh karyawan / segmen tertentu, dengan tracking acknowledgment.

**Primary user:** HR Admin, Manager

**Key features:**
- Broadcast all / per company / per dept / per branch / per role / custom segment
- Rich text + image + attachment
- Schedule publish + expiry date
- Pin priority (sticky di top)
- Read receipt + acknowledgment tracking (untuk policy compliance)
- Polling / quick survey (single/multi choice)
- Birthday + work anniversary auto-broadcast
- Hari raya / cuti bersama auto-broadcast
- New hire welcome auto-broadcast

**Workflows:**
- HR buat announcement → pilih segment → schedule → publish → push notif + email → tracking

**Key entities:** `announcements`, `announcement_recipients`, `announcement_acknowledgments`, `announcement_polls`, `announcement_poll_responses`

**API:** `/api/v1/announcements`, `/api/v1/announcements/{id}/acknowledge`

**Mobile/WA:** notification feed + ack button

---

## M07 — Employee Self-Service (ESS)

**Purpose:** Portal & app untuk karyawan urus diri sendiri tanpa nyangkut ke HR.

**Primary user:** Employee

**Key features:**
- Dashboard ringkas: shift hari ini, saldo cuti, slip terbaru, request pending
- View slip gaji + tax form (1721-A1)
- View kontrak aktif + download
- Request cuti / izin / sakit
- Request lembur (pre/post)
- Request reimbursement (M18)
- Request shift swap (M09)
- View attendance log + correction request
- Update profile (alamat, contact, bank — dengan HR approval untuk sensitif field)
- Family info update + dependents BPJS
- Download HR letter dirinya sendiri
- View team calendar (siapa cuti kapan)
- View org chart
- Birthday list (siapa ulang tahun bulan ini)
- Mobile app iOS + Android
- Web portal responsive
- WhatsApp bot interaksi (clock-in, leave, slip — Phase 5+)

**Workflows:**
- Login → dashboard → action → notif ke HR/manager

**Key entities:** reuses semua entity, no new entity besides `notification_preferences`

**API:** `/api/v1/me/*` (semua endpoint personal scope)

**Mobile/WA:** **primary use case**

---

## M08 — Digital Signature

**Purpose:** Tanda tangan elektronik untuk kontrak, surat, dan dokumen HR — legally binding.

**Primary user:** Employee + HR Admin

**Key features:**
- Adapter dinamis: BSrE (BSSN), Privy ID, PrivySign, generic PKI
- Single signature & multi-party signature
- OTP via SMS/email untuk verifikasi penandatangan
- Audit trail penandatanganan (IP, device, timestamp)
- Sertifikat digital embed di PDF
- Bulk sign (HR sign 100 surat sekaligus)

**Workflows:**
- HR generate letter (M04) → request signature → notif karyawan → OTP verify → sign → archive

**Key entities:** `signature_requests`, `signature_provider_configs` (dinamis per tenant), `signed_documents`

**API:** `/api/v1/signature/request`, `/api/v1/signature/{id}/sign`

**Mobile/WA:** receive sign request + verify OTP + tap-to-sign

---

## M09 — Shift & Schedule Management

**Purpose:** Atur jadwal kerja karyawan dengan support shift kompleks (manufaktur, F&B, healthcare).

**Primary user:** HR Admin, Supervisor

**Key features:**
- Shift type: fixed (9-5), rotating (pagi/siang/malam), split shift (potong istirahat panjang), flexible (no shift), on-call
- Shift pattern builder (e.g., 6-1, 5-2, 4-3 rotasi)
- Working hours per shift + break time
- Schedule template per dept/role
- Auto-roster generator (assign shift ke karyawan otomatis berdasarkan pattern)
- Manual roster edit
- Shift swap antar karyawan (dengan approval manager)
- Calendar view weekly/monthly
- Conflict detection (overlap, jam kerja max per minggu)
- UU CK compliance: max 40 jam/minggu (atau 56 dengan lembur)

**Workflows:**
- HR setup shift type → buat schedule template → assign karyawan → publish ke ESS
- Karyawan request swap → counter-party approve → manager approve → schedule update

**Key entities:** `shifts`, `shift_patterns`, `schedule_templates`, `employee_schedules`, `shift_swap_requests`

**API:** `/api/v1/shifts`, `/api/v1/schedules`, `/api/v1/schedules/swap`

**Mobile/WA:** view schedule, request swap

---

## M10 — Attendance (Clock-in/Out)

**Purpose:** Pencatatan kehadiran karyawan akurat, anti-cheat, support multi-location & WFH.

**Primary user:** Employee (clock), HR (monitor)

**Key features:**
- GPS check-in + geofence radius (configurable per branch)
- Selfie photo with timestamp watermark
- Face recognition + anti-spoofing (Phase 5+, deteksi foto/video di depan kamera)
- Fingerprint device integration (SDK ZKTeco, Suprema, dll.) untuk on-site
- QR code scan (kios mode untuk warehouse/pabrik)
- Multi-location radius (cabang berbeda, project site)
- WFH / WFA mode (no geofence)
- Real-time live tracking dashboard (HRD lihat semua karyawan di map)
- **Mock GPS / fake location detection**
- **Offline mode + auto-sync** saat ada koneksi
- Late / early-leave / absent auto-flag
- Auto-deduct salary on absent (configurable)
- Attendance correction request workflow
- Bulk approval correction
- Daily attendance summary (kehadiran, terlambat, absen, lembur)

**Workflows:**
- Karyawan buka app → tap clock-in → GPS check → selfie → submit → record + log
- Offline: cache local → sync saat online → server validate timestamp
- Lupa absen → request correction → manager approve → record update

**Key entities:** `attendance_logs`, `attendance_corrections`, `geofence_zones`, `fingerprint_devices`, `face_templates`

**API:** `/api/v1/attendance/check-in`, `/api/v1/attendance/check-out`, `/api/v1/attendance/correction`

**Mobile/WA:** **PRIMARY USE CASE** — clock-in via WA bot dengan share location

---

## M11 — Overtime Management

**Purpose:** Kelola lembur karyawan sesuai Kepmenaker 102/2004 dengan auto-calculate.

**Primary user:** Employee (request), Manager (approve), HR (monitor)

**Key features:**
- Request lembur pre (sebelum kerja overtime) atau post (sesudah)
- Auto-calculate berdasarkan **Kepmenaker 102/2004**:
  - Hari kerja: jam 1 = 1.5x, jam 2+ = 2x upah/jam
  - Hari libur: jam 1-7 = 2x, jam 8 = 3x, jam 9-10 = 4x
  - Upah/jam = upah bulanan / 173
- Tarif beda weekday vs weekend/libur
- Cap lembur per bulan / per minggu (UU CK: max 4 jam/hari, 18 jam/minggu)
- Lembur ditukar cuti pengganti (compensatory time off)
- Budget lembur per dept (warning saat over)
- Auto-flow ke payroll
- Linked dengan shift (M09) dan attendance (M10)
- Bukti foto/dokumen pekerjaan lembur (opsional)

**Workflows:**
- Karyawan request lembur → manager approve → execute → attendance record overtime hours → payroll auto-calc

**Key entities:** `overtime_requests`, `overtime_rules`, `overtime_logs`

**API:** `/api/v1/overtime/request`, `/api/v1/overtime/{id}/approve`

**Mobile/WA:** request + approve dari WA

---

## M12 — Leave Management

**Purpose:** Kelola cuti karyawan dengan saldo per user, multi-type, auto-deduct payroll.

**Primary user:** Employee (request), Manager + HR (approve)

**Key features:**
- Tipe cuti default Indonesia: tahunan (12 hari), sakit (dengan/tanpa surdok), melahirkan (3 bulan), ayah (2 hari), menikah (3 hari), khusus (orangtua/anak meninggal, dll.), unpaid leave
- Custom leave type per perusahaan
- Saldo per user dengan accrual rule (e.g., 1 hari per bulan, atau 12 hari granted di awal tahun)
- Carry-over saldo tahun depan dengan expiry (e.g., expire Maret tahun depan)
- Half-day leave
- Cuti bersama auto-deduct dari saldo tahunan (governmental setting)
- Unpaid leave auto-deduct payroll
- Approval workflow (manager → HR untuk cuti panjang)
- Leave conflict warning (overlap team)
- **Cuti ulang tahun** (perk differentiator)
- Document upload untuk cuti sakit (surdok)
- Calendar view team
- Email + push notif

**Workflows:**
- Karyawan request → cek saldo → approval workflow → record + saldo deducted → notif team
- Awal tahun: HR jalankan accrual → semua karyawan dapat saldo baru

**Key entities:** `leave_types`, `leave_balances`, `leave_requests`, `leave_accruals`, `leave_carry_overs`

**API:** `/api/v1/leave/types`, `/api/v1/leave/balance`, `/api/v1/leave/request`

**Mobile/WA:** request via WA "cuti besok sakit"

---

## M13 — Holiday Calendar

**Purpose:** Master kalender hari libur Indonesia + custom per perusahaan/cabang.

**Primary user:** HR Admin (setup), semua user (read)

**Key features:**
- Pre-loaded tanggal merah Indonesia (auto-update per tahun)
- Cuti bersama dari Keputusan Bersama (SKB 3 Menteri)
- Custom holiday per company/branch (e.g., ulang tahun perusahaan)
- Religion-specific holiday (Idul Adha, Natal, Nyepi, Waisak, Imlek) — opsional show by religion karyawan
- Calendar export (iCal / Google Calendar)
- Link ke shift (M09) — auto-skip schedule di tanggal libur

**Workflows:**
- HR konfirmasi holiday tahun depan → publish → karyawan lihat di kalender

**Key entities:** `holidays`, `holiday_calendars`, `holiday_overrides`

**API:** `/api/v1/holidays`, `/api/v1/holidays/calendar/{year}`

**Mobile/WA:** view kalender libur

---

## M14 — Payroll Engine

**Purpose:** Hitung gaji karyawan dengan komponen custom, formula builder, multi-period, integrasi attendance/overtime/leave/reimburse.

**Primary user:** HR Admin (run), Finance (approve)

**Key features:**
- Komponen gaji custom unlimited (earnings + deductions + benefits)
- Formula builder excel-like (e.g., `gaji_pokok * 0.1` untuk transport)
- Earning types: gaji pokok, tunjangan tetap, tunjangan variable, komisi, bonus, THR
- Deduction types: BPJS karyawan, PPh 21, pinjaman, unpaid leave, denda
- Benefit (non-tunai): asuransi tambahan, voucher
- Pro-rata mid-month join/resign
- Multi-period (bulanan default, support mingguan/dua-mingguan/harian)
- Off-cycle payroll (bonus, THR, koreksi)
- THR generator (1x gaji + tunjangan tetap, prorate)
- Variable allowance from attendance (uang makan per hadir, transport per hadir)
- Multi-currency untuk expat
- Payslip PDF + email + WhatsApp + password lock (NIK/DOB)
- Payroll lock + approval (multi-level)
- Audit trail per perubahan
- Bulk-edit support
- Payroll re-run (idempotent)
- Background queue (5 menit untuk 500 karyawan)

**Workflows (DETAIL di docs 06):**
1. Cut-off attendance + overtime + leave + reimburse di-finalize
2. HR trigger payroll run
3. Engine fetch semua input → calculate per karyawan
4. Apply PPh 21 (M15) + BPJS (M16)
5. Generate payslip preview
6. HR review + approve
7. Finance approve disbursement
8. Bank batch file (M17) atau instant disburse
9. Karyawan terima slip + transfer

**Key entities:** `payroll_runs`, `payroll_items` (per karyawan per run), `payroll_components` (master earning/deduction), `payroll_item_components`, `payroll_formulas`, `payslips`

**API:** `/api/v1/payroll/runs`, `/api/v1/payroll/runs/{id}/items`, `/api/v1/payroll/runs/{id}/approve`

**Mobile/WA:** karyawan view slip; HR approve via WA (Phase 5+)

---

## M15 — Tax PPh 21 / 26

**Purpose:** Hitung pajak penghasilan karyawan sesuai aturan Indonesia terbaru, generate form, integrate ke DJP.

**Primary user:** HR Admin, Finance, Tax Officer

**Key features:**
- **PPh 21 TER 2024** (PMK 168/2023) — tarif efektif rata-rata bulanan
- Metode Gross / Nett / Gross-up (untuk THR/bonus, dan ada perusahaan mau cover pajak)
- PPh 21 final (pesangon, severance, JHT cash-out)
- PPh 26 untuk WNA non-resident
- Status PTKP (TK/0, TK/1, ..., K/3 — sampai 3 tanggungan)
- Form 1721-A1 generator (bukti potong tahunan PDF)
- Form 1721-I (laporan bulanan)
- e-Bupot integration / export ke format DJP
- Direct submit ke DJP Coretax API (Phase 5+, saat API live)
- Auto-update saat aturan baru
- Tax simulation calculator (HR + karyawan)
- DTP (Ditanggung Pemerintah) scenarios

**Workflows:**
- Payroll run → PPh 21 service hitung per karyawan → simpan bukti potong → akhir tahun generate 1721-A1
- HR submit bulanan → generate 1721-I → upload e-Bupot atau direct API

**Key entities:** `tax_settings` (per company), `ptkp_rules`, `ter_rates` (TER 2024 brackets), `tax_calculations` (per payroll item), `tax_forms` (1721-A1, 1721-I), `tax_periods`

**API:** `/api/v1/tax/calculate`, `/api/v1/tax/forms/{year}`, `/api/v1/tax/1721-a1/{employee}`

**Mobile/WA:** karyawan download 1721-A1 sendiri

---

## M16 — BPJS Kesehatan & Ketenagakerjaan

**Purpose:** Hitung iuran BPJS sesuai aturan, generate file SIPP, integrate ke BPJS.

**Primary user:** HR Admin, Finance

**Key features:**
- **BPJS Kesehatan**: 4% employer + 1% employee, cap upah Rp 12 juta, max 5 dependents (suami/istri + 3 anak)
- **BPJS Ketenagakerjaan**:
  - JHT (Jaminan Hari Tua): 3.7% employer + 2% employee
  - JP (Jaminan Pensiun): 2% employer + 1% employee, cap upah Rp 10.042.300 (2026 update)
  - JKK (Jaminan Kecelakaan Kerja): 0.24% – 1.74% employer (per tingkat risiko)
  - JKM (Jaminan Kematian): 0.3% employer
- Cap upah BPJS (auto-update)
- Dependents (suami/istri + 3 anak)
- Auto-generate file SIPP (XLSX upload-ready ke BPJSTK)
- Direct submit ke SIPP API (Phase 5+, kalau tersedia)
- Klaim JKK / JKM workflow (HR bantu karyawan klaim)
- Iuran berbeda per cabang/UMK (untuk perusahaan multi-cabang lintas UMK)
- Voluntary BPJS untuk freelancer

**Workflows:**
- Payroll run → BPJS service hitung iuran → simpan → bulan berikutnya generate SIPP → upload BPJSTK + bayar
- Karyawan klaim JHT → HR submit data → BPJSTK proses

**Key entities:** `bpjs_settings`, `bpjs_rates` (history rate per tahun), `bpjs_calculations`, `bpjs_dependents`, `bpjs_claims`, `bpjs_company_profiles` (NPP, kode cabang BPJS)

**API:** `/api/v1/bpjs/calculate`, `/api/v1/bpjs/sipp-export`, `/api/v1/bpjs/claims`

**Mobile/WA:** karyawan lihat iuran BPJS-nya

---

## M17 — Bank Disbursement

**Purpose:** Bayar gaji ke rekening karyawan via batch file bank atau instant API.

**Primary user:** Finance

**Key features:**
- **Batch file format** per bank:
  - BCA KlikBCA Bisnis (CSV)
  - Mandiri Cash Management (TXT)
  - BNI Direct (XLSX)
  - BRI CMS (CSV)
  - CIMB BizChannel (CSV)
  - Permata Net (TXT)
  - Danamon (CSV)
- **Instant disbursement** via Flip / Xendit API (Phase 2+)
- **Bank account validation** (cek nomor rekening valid sebelum disburse) — Phase 5+
- Re-run failed transfer + retry
- Disbursement history + reconciliation
- Multi-bank split (e.g., 70% ke bank A, 30% ke bank B)
- Approval finance sebelum execute
- Confirmation upload (slip transfer dari bank)
- Cost calculation (admin fee transfer)

**Workflows:**
- Payroll approved → Finance generate batch file → upload ke iBank → execute → upload bukti
- Atau: instant disburse → Flip/Xendit API → real-time confirmation

**Key entities:** `disbursement_runs`, `disbursement_items`, `disbursement_files`, `bank_adapter_configs` (dinamis)

**API:** `/api/v1/disbursement/generate-file`, `/api/v1/disbursement/instant`

**Mobile/WA:** karyawan get notif "gaji sudah ditransfer"

---

## M18 — Reimbursement & Expense

**Purpose:** Kelola request expense karyawan dengan policy, budget, dan flow ke payroll atau separate disburse.

**Primary user:** Employee (request), Manager + Finance (approve)

**Key features:**
- Kategori expense (transport, meals, medical, entertainment, dll.)
- Policy limit per kategori per role (e.g., transport max Rp 100k/hari)
- Budget tracking real-time per dept
- Upload bukti foto/PDF
- **OCR receipt** (auto-extract nominal & vendor & date) — Phase 5+
- Flexible approval rules (e.g., > Rp 1jt = 2 approval)
- Auto-flow ke payroll atau separate disbursement
- Multi-currency
- Per-diem (uang harian dinas)
- Corporate card reconciliation (Phase 6+)
- Tax-deductible flag (untuk reporting PPh 21)
- Linked dengan travel/SPPD (M26)

**Workflows:**
- Karyawan kerjaan beli bensin → foto struk → submit → OCR extract → manager approve → finance approve → disburse atau next payroll

**Key entities:** `reimbursement_categories`, `reimbursement_policies`, `reimbursement_budgets`, `reimbursement_requests`, `reimbursement_items`

**API:** `/api/v1/reimbursement/request`, `/api/v1/reimbursement/policies`

**Mobile/WA:** **PRIMARY USE CASE** — foto struk via WA → OCR → auto-submit

---

## M19 — Recruitment / ATS

**Purpose:** Mengelola lowongan, sourcing, screening, interview, hingga offer.

**Primary user:** HR Recruiter, Hiring Manager

**Key features:**
- Career page publik (subdomain `karir.companyname.id`)
- Job posting dengan rich description
- Share job ke channel (LinkedIn, Jobstreet, Glints — via integration)
- LinkedIn integration (import lowongan)
- **AI CV screening + match score** (Phase 5+, via LLM adapter)
- Multi-filter (skill, lokasi, exp, salary, education)
- Pipeline kanban (drag-drop stage: applied → screen → interview → offer → hired)
- Interview scheduler + calendar invite + Google Meet link
- Interview feedback form + scorecard
- **1-click WhatsApp/Email blast** kandidat (differentiator)
- Talent pool / candidate database (reusable)
- Offer letter generator + e-sign (M08)
- Referral program tracking
- Auto-reject email template
- Background check integration (Phase 6+)
- Diversity & EEO reporting (untuk perusahaan multi-cabang internasional)

**Workflows:**
- Hiring manager request → HR buat job posting → publish → kandidat apply → AI screen → HR shortlist → interview → offer → onboarding (M20)

**Key entities:** `job_postings`, `candidates`, `applications`, `application_stages`, `interviews`, `interview_feedbacks`, `offers`, `referrals`, `talent_pool_tags`

**API:** `/api/v1/jobs`, `/api/v1/candidates`, `/api/v1/applications`, `/api/v1/interviews`

**Mobile/WA:** kandidat apply via mobile, HR notif

---

## M20 — Onboarding & Probation

**Purpose:** Welcome new hire, collect dokumen, assign asset, run probation review.

**Primary user:** HR Admin, New Hire, Manager

**Key features:**
- Onboarding checklist per role (HR setup template)
- Document collection (KTP, ijazah, NPWP, BPJS, surat sehat, dll.)
- Asset assignment workflow (laptop, ID card, kartu nama)
- Welcome email + intro slide / video
- Buddy assignment (mentor selama 90 hari)
- Probation 30/60/90-day review
- Auto-end probation reminder
- Onboarding survey (eNPS pertama)
- E-learning mandatory (link ke LMS M24)
- E-sign kontrak (M08)

**Workflows:**
- Offer accepted → HR setup onboarding → new hire join hari H → checklist → 30/60/90 review → permanent atau extend probation

**Key entities:** `onboarding_templates`, `onboarding_checklists`, `onboarding_tasks`, `probation_reviews`

**API:** `/api/v1/onboarding`, `/api/v1/onboarding/{id}/tasks`

**Mobile/WA:** new hire complete checklist via app

---

## M21 — OKR & KPI

**Purpose:** Goal-setting framework dengan cascading dari company → individual.

**Primary user:** semua karyawan

**Key features:**
- Cascading goals unlimited level (company → dept → team → individual)
- Quarterly atau annual cycle
- Key Result dengan target measurable
- KPI weight & scoring (untuk performance review M22)
- Progress update (manual % atau auto-calc dari data)
- Check-in mingguan / bulanan
- Alignment view (siapa support siapa)
- Reminder in-app + email
- OKR retrospective tool (lesson learned)
- Public vs private OKR (transparency)

**Workflows:**
- Quarter start: CEO set company OKR → cascade → individual OKR → weekly check-in → quarter end review

**Key entities:** `okr_cycles`, `objectives`, `key_results`, `okr_check_ins`, `okr_alignments`

**API:** `/api/v1/okr/objectives`, `/api/v1/okr/check-ins`

**Mobile/WA:** weekly check-in via app

---

## M22 — Performance Appraisal 360°

**Purpose:** Review performa karyawan secara komprehensif dengan multi-perspektif.

**Primary user:** semua karyawan (review subject + reviewer)

**Key features:**
- 360° review (self, peer, subordinate, manager)
- Custom appraisal form builder (rating + open question + competency)
- **9-box matrix** (performance × potential)
- Calibration meeting tool (HR + leaders align)
- Cycle scheduler (annual, semester, quarterly)
- Goal-linked appraisal (link ke OKR M21)
- Anonymous peer feedback option
- Continuous feedback (kudos anytime, bukan annual)
- **AI summary 360 feedback** (Phase 5+) — LLM summarize feedback text
- Calibration history
- Compa-ratio analysis (salary vs market)

**Workflows:**
- HR start cycle → assign reviewer → karyawan submit self + peer + manager → calibration meeting → final rating → linked ke salary increase / promotion

**Key entities:** `appraisal_cycles`, `appraisal_forms`, `appraisal_questions`, `appraisal_responses`, `appraisal_ratings`, `nine_box_placements`, `continuous_feedbacks`

**API:** `/api/v1/appraisal/cycles`, `/api/v1/appraisal/forms`, `/api/v1/appraisal/responses`

**Mobile/WA:** submit feedback via app

---

## M23 — Competency & Succession

**Purpose:** Map skill karyawan, identify gap, dan plan succession.

**Primary user:** HR, Manager, C-level

**Key features:**
- Competency framework (e.g., Lominger 67 competencies)
- Skill matrix per role
- Skill gap analysis (required vs actual per karyawan)
- Career path visualization (next role possibilities)
- **Succession planning** (talent pool per critical position)
- Promotion-ready dashboard
- IDP (Individual Development Plan) per karyawan
- Linked ke LMS M24 (training untuk close gap)

**Workflows:**
- HR define competency per role → assess karyawan → identify gap → assign training → re-assess

**Key entities:** `competencies`, `competency_levels`, `role_competencies`, `employee_competencies`, `career_paths`, `succession_plans`, `idp_plans`

**API:** `/api/v1/competencies`, `/api/v1/succession-plans`

**Mobile/WA:** view own competency profile + career path

---

## M24 — Learning & Development (LMS)

**Purpose:** Internal learning platform untuk training karyawan.

**Primary user:** semua karyawan + L&D admin

**Key features:**
- Course catalog (video, PDF, SCORM, link external)
- Quiz & assessment
- Certification + expiry tracking
- Training hours tracking
- External training expense link (M18)
- Mandatory training auto-assign per role (e.g., security awareness, anti-bribery, K3)
- Course rating + feedback
- **AI-generated quiz dari materi** (Phase 5+)
- Learning path (course bundle untuk role)
- Compliance reporting (siapa lulus mandatory training)

**Workflows:**
- L&D upload course → assign ke karyawan / role → karyawan complete → quiz → certificate → record

**Key entities:** `courses`, `course_modules`, `quizzes`, `quiz_questions`, `enrollments`, `course_completions`, `certificates`, `learning_paths`

**API:** `/api/v1/courses`, `/api/v1/enrollments`, `/api/v1/certificates`

**Mobile/WA:** stream video + take quiz di app

---

## M25 — Asset Management

**Purpose:** Track aset perusahaan yang dipegang karyawan (laptop, ID card, kendaraan dinas).

**Primary user:** GA / Asset officer, HR, Karyawan

**Key features:**
- Asset register (kategori, brand, serial, kondisi, warranty)
- Assignment to employee + handover form (PDF e-sign opsional)
- Return on offboarding (auto-checklist di M28)
- Maintenance schedule
- Depreciation tracking (untuk accounting integration)
- Service record (log servis kendaraan, repair laptop)
- Asset request (karyawan butuh laptop baru)

**Workflows:**
- New hire onboarding → assign laptop → handover form sign → karyawan terima
- Exit → return laptop → inspect → record

**Key entities:** `assets`, `asset_categories`, `asset_assignments`, `asset_maintenances`, `asset_requests`

**API:** `/api/v1/assets`, `/api/v1/assets/{id}/assign`

**Mobile/WA:** view aset assigned ke saya

---

## M26 — Travel / Business Trip (SPPD)

**Purpose:** Manage perjalanan dinas karyawan dari request hingga settlement.

**Primary user:** Employee, Manager, Finance

**Key features:**
- Trip request workflow (tujuan, tanggal, tujuan bisnis, mode transport)
- Cash advance (uang muka)
- Per-diem auto-calc (per kota / per role, e.g., uang harian Jakarta vs Manado)
- Settlement & expense report (linked M18)
- SPPD letter auto-generate (M04)
- Travel agent integration (Traveloka Biz) — Phase 6+
- Hotel + flight booking history
- Travel policy enforcement (kelas pesawat, hotel rate max)
- Mileage tracking untuk kendaraan pribadi

**Workflows:**
- Karyawan request → manager approve → finance approve advance → trip executed → kembali → submit settlement → finance verify

**Key entities:** `travel_requests`, `travel_advances`, `per_diem_rates`, `travel_settlements`

**API:** `/api/v1/travels/request`, `/api/v1/travels/settlement`

**Mobile/WA:** submit ekspense trip via WA dengan foto struk

---

## M27 — Loan / Koperasi Karyawan

**Purpose:** Pinjaman karyawan dengan auto-deduct dari payroll.

**Primary user:** Employee, HR, Finance, Koperasi admin

**Key features:**
- Cash advance request (≤1 bulan gaji)
- Installment loan + interest (multi-bulan)
- Auto-deduct payroll per cicilan
- Loan policy (max % gaji, max tenor, min tenure required)
- **Koperasi simpan-pinjam mode** (differentiator) — anggota simpan iuran pokok + wajib + sukarela; bunga pinjaman jadi SHU
- Loan history per karyawan
- Outstanding balance dashboard
- Default management (karyawan resign masih ada hutang)

**Workflows:**
- Karyawan request loan → HR review → approval (manager + CFO untuk nominal besar) → disburse → cicilan auto-deduct
- Koperasi: setor simpanan tiap bulan → akhir tahun bagi SHU

**Key entities:** `loans`, `loan_policies`, `loan_installments`, `cooperative_memberships`, `cooperative_savings`, `cooperative_shu_distributions`

**API:** `/api/v1/loans/request`, `/api/v1/loans/{id}/installments`

**Mobile/WA:** request via app, lihat outstanding

---

## M28 — Exit Management

**Purpose:** Proses karyawan resign / PHK dengan clearance lengkap.

**Primary user:** Employee (resign), HR, Manager, Finance, IT, GA

**Key features:**
- Resignation request workflow
- Clearance checklist (asset return, finance settlement, knowledge handover)
- Exit interview form (anonymous opsi)
- Final payment (pesangon, sisa cuti, THR prorate)
- **Pesangon UU CK calculator** (uang pesangon + uang penghargaan masa kerja + uang penggantian hak)
- Paklaring auto-generate (M04)
- Access revocation checklist (email, GitHub, Slack, SaaS account)
- Boomerang tag (rehirable / no-rehire)
- Last working day reminder ke team
- Reason analysis (untuk turnover insight)

**Workflows:**
- Karyawan submit resign → HR approve → clearance checklist → exit interview → last day → final payment → paklaring

**Key entities:** `exit_requests`, `exit_clearance_items`, `exit_interviews`, `severance_calculations`

**API:** `/api/v1/exits/request`, `/api/v1/exits/{id}/clearance`

**Mobile/WA:** exit interview via app

---

## M29 — Analytics & Reporting

**Purpose:** Visualisasi data + insight untuk decision-making.

**Primary user:** HR, Manager, C-level

**Key features:**
- **Pre-built dashboards:**
  - Attendance (kehadiran, terlambat, absen, lembur)
  - Payroll cost by dept / cost center / time
  - Headcount + turnover rate + retention
  - Performance distribution (9-box, rating distribution)
  - Recruitment funnel + time-to-hire
  - Leave usage + balance
  - Training completion + cost
- Custom report builder (drag-drop)
- Scheduled report email (daily, weekly, monthly)
- Export Excel + PDF + CSV
- **AI insight & narrative summary** (Phase 5+) — "turnover dept A naik 15% karena…"
- **Predictive analytics (turnover risk)** — model ML flag karyawan high-risk
- **Compensation benchmark vs market** (Phase 6+, data partner)
- BI integration (Metabase, Looker, Power BI via REST API)
- Real-time vs batch reporting (heavy queries di queue)

**Workflows:**
- HR open dashboard → filter → drill down → export atau schedule
- AI daily summary → email "kemarin 3 cuti sakit, 1 resign, anomaly tinggi dept B"

**Key entities:** `report_templates`, `report_schedules`, `report_executions`, `analytics_caches`, `predictions`, `benchmarks`

**API:** `/api/v1/reports`, `/api/v1/reports/{id}/run`, `/api/v1/analytics/dashboards`

**Mobile/WA:** ringkasan harian via WA

---

## Cross-Cutting Modules (Bukan modul fitur, tapi backbone)

### CX01 — Notification Service
Multi-channel notification: in-app, push (FCM), email, WhatsApp, SMS. Tenant + user dapat set preferensi per event type.

### CX02 — Audit Log
Spatie Activitylog + custom logger. Capture: create/update/delete event, sensitive view (PII access), login event.

### CX03 — File Storage
S3-compatible adapter. Versioning + encryption + signed URL.

### CX04 — Webhook (outbound)
Tenant register URL per event → engine fire with HMAC signature. Retry 5x exponential.

### CX05 — API Gateway
Rate limit + API key management + request log untuk integration partner.

### CX06 — License Gate
Middleware verify license v3 di setiap request. Cache hasil per 5 menit.

### CX07 — Tenant Management
Onboarding self-serve, billing, plan upgrade, suspend, data export, data deletion.

### CX08 — Internal Admin Panel
Super-admin panel terpisah untuk: tenant management, billing, support, system health.
