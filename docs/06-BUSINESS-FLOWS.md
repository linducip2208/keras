# 06 — Business Flows (End-to-End)

> Alur bisnis lengkap per proses utama HRIS. Setiap flow dijelaskan dengan: trigger, actor, step-by-step, system actions, integrations, exception handling, dan output.

## Flow Index

| # | Flow | Modul Terlibat |
|---|---|---|
| F01 | Employee Lifecycle (hire → exit) | M01, M02, M03, M19, M20, M28 |
| F02 | Daily Attendance | M09, M10, M11, M07 |
| F03 | Leave Request | M12, M05, M07, M14 |
| F04 | Overtime Request & Payroll Integration | M11, M10, M14, M05 |
| F05 | Monthly Payroll Cycle | M14, M15, M16, M17, M10, M11, M12, M18, M27 |
| F06 | Recruitment (Sourcing → Hiring) | M19, M20 |
| F07 | Reimbursement Request | M18, M05, M14 |
| F08 | Travel / SPPD | M26, M05, M18 |
| F09 | Loan & Auto-Deduct | M27, M05, M14 |
| F10 | Performance Cycle (OKR → Appraisal → Calibration) | M21, M22, M23 |
| F11 | Learning Path & Certification | M24, M23 |
| F12 | Approval Workflow (generic) | M05 |
| F13 | Resignation & Exit | M28, M04, M14, M25 |
| F14 | Onboarding | M20, M03, M25, M24 |
| F15 | Tax Year-End (1721-A1) | M15, M14 |
| F16 | BPJS Monthly Reporting | M16, M14 |
| F17 | Multi-Company Group Consolidation | M02, M14, M29 |

---

## F01 — Employee Lifecycle

**Trigger:** Posisi terbuka di company → HR hire kandidat baru

```
[HIRING REQUEST]
HR Manager / Hiring Manager
        │ open requisition
        ▼
[F06 Recruitment Flow]
        │ candidate selected, offer accepted
        ▼
[CREATE EMPLOYEE RECORD] (M01)
- generate employee_no
- input personal data (NIK, NPWP, alamat)
- assign company, branch, dept, position (M02)
- update employee_assignments
        ▼
[CREATE CONTRACT] (M03)
- pilih jenis kontrak (PKWT/PKWTT/Probation)
- isi salary base, allowance
- start_date, end_date, probation_end_date
- generate contract PDF dari template
- request e-sign (M08) — optional
        ▼
[F14 Onboarding Flow] (M20)
- assign onboarding template
- collect dokumen (M01)
- assign asset (M25)
- schedule probation review
- enroll mandatory training (M24)
        ▼
[ACTIVE EMPLOYMENT]
- daily attendance (F02)
- monthly payroll (F05)
- ongoing leave/overtime/reimburse (F03/F04/F07)
- performance cycles (F10)
        ▼
[EXIT] (F13)
```

**System actions:**
- Auto-create user account untuk ESS (random password, magic link invite)
- Auto-enroll BPJS (M16) sesuai kebijakan company
- Auto-create leave balance (M12) berdasarkan tenure
- Trigger event `employee.joined` → webhook (CX04)
- Audit log semua perubahan

**Exception:**
- Duplicate NIK detected → block + alert
- Kontrak kosong saat join → status `pending_contract` blok payroll
- Onboarding incomplete > 30 hari → escalate ke HR Manager

---

## F02 — Daily Attendance

**Trigger:** Karyawan mulai/akhiri jam kerja

```
[KARYAWAN CHECK-IN]
Karyawan via Mobile App / WhatsApp / Fingerprint / QR / Web ESS
        │
        ▼
[VALIDASI] (M10 + M09)
- ada schedule hari ini?
- Mode mana (GPS / Fingerprint / Face / QR)?
- GPS dalam geofence?
- Bukan mock GPS?
- Selfie cocok (face recognition Phase 5+)?
- Bukan double clock-in?
        │
        ├─ FAIL → tampilkan error + log
        │
        ▼
[RECORD ATTENDANCE_LOG]
- check_in_at, lat/long, photo, method
- compute: is_late, late_minutes
- jika offline mode: cache local → queue sync
        │
        ▼
[NOTIF] (M06)
- ke manager kalau very late (>30 min late)
        │
        ▼
[KARYAWAN BEKERJA]
        │
        ▼
[CHECK-OUT]
- same validation
- compute: worked_minutes
- jika ada planned overtime (M11), capture overtime_minutes
        │
        ▼
[END OF DAY] (otomatis cron 23:59)
- karyawan absent: status='absent'
- worked < shift hours: status='half_day' atau alert
- karyawan ada approved leave: status='leave'
- karyawan kerja > shift hours tanpa OT request: alert manager
        ▼
[AGGREGATE]
- update aggregate harian/bulanan di payroll preview
```

**Edge cases:**
- **Karyawan lupa absen**: submit attendance correction (M10) → manager approve → record retroactive
- **Offline saat clock-in**: simpan di Hive local → sync saat online → server validate timestamp drift <2 jam
- **Mock GPS detected**: flag attendance, masih record tapi `is_mock_gps=true`, alert HR
- **Cross-midnight shift**: handle via `shift.crosses_midnight` flag; attendance log tetap punya 1 row per shift period (bukan per calendar day)

**WhatsApp flow:**
```
Karyawan: [share location to bot]
Bot: "Halo Joko! Anda mau check-in untuk shift 09:00 hari ini?"
Karyawan: "Ya"
Bot: "Silakan kirim selfie sekarang"
Karyawan: [foto]
Bot: "✅ Check-in jam 09:05. Selamat bekerja!"
```

---

## F03 — Leave Request

**Trigger:** Karyawan ingin cuti

```
[KARYAWAN REQUEST CUTI] (M07/M12)
- pilih jenis cuti (tahunan/sakit/melahirkan/dll)
- pilih tanggal mulai & selesai
- upload attachment (surdok untuk sakit)
- isi delegate (siapa cover kerja saat cuti)
- isi contact during leave
        │
        ▼
[VALIDASI] (M12)
- saldo cukup? (untuk paid leave)
- min notice days terpenuhi? (e.g., tahunan minimal H-7)
- max consecutive days OK?
- bentrok dengan tanggal libur / cuti bersama?
- attachment required untuk jenis ini?
- leave_conflict_warning: ada team mate cuti sama-sama?
        │
        ├─ FAIL → display error, fix → resubmit
        │
        ▼
[CREATE LEAVE_REQUEST, status=pending]
- update leave_balance.pending += days
        │
        ▼
[F12 Approval Workflow]
- routing: manager → (HR untuk cuti panjang) → ...
- approver notif via push + WA + email
        │
        ├─ REJECTED → leave_balance.pending -= days
        │            → notif karyawan + reason
        │
        ▼ APPROVED
[UPDATE STATE]
- leave_requests.status = 'approved'
- leave_balances.pending -= days, used += days
- generate calendar entry di attendance (status='leave' di tgl tsb)
- skip attendance auto-deduct di tanggal cuti
- notif team + delegate
        │
        ▼
[CUTI BERJALAN]
- pada tanggal cuti, M10 attendance generate row status='leave', tidak hitung absent
- jika unpaid leave: M14 payroll auto-deduct
        ▼
[SETELAH CUTI SELESAI]
- jika cuti sakit > batas surdok (e.g., 3 hari): require surdok upload retroactive
```

**Cuti darurat (sakit hari H):**
- Karyawan WA bot "saya sakit hari ini"
- Bot bikin draft leave request
- Approval expedited (timeout 2 jam → auto-approve, manager retroactive check)

**Carry-over (akhir tahun):**
- Job berjalan 31 Des
- Sisa saldo cuti tahunan → carry_over (max sesuai policy)
- Carry_over expires sesuai policy (e.g., 3 bulan)
- Job kedua di expiry date: forfeit sisa carry_over

---

## F04 — Overtime Request

**Trigger:** Karyawan/manager rencanakan lembur

```
[REQUEST OVERTIME] (M11)
Mode A — Pre-OT (sebelum kerja):
- karyawan/manager submit form
- alasan, tanggal, jam planned

Mode B — Post-OT (sesudah kerja):
- karyawan submit dengan actual hours
- manager validate vs attendance log
        │
        ▼
[VALIDATE] (M11)
- date valid (bukan future > 7 days)?
- planned hours <= max per day?
- accumulated hours bulan <= max per month?
- ada approved leave di hari itu? → conflict
        │
        ▼
[APPROVAL] (F12)
- manager (level 1) → HR (level 2 if >4 jam)
        │
        ▼ APPROVED
[EXECUTION]
- karyawan kerja overtime
- M10 attendance capture actual end time
- M11 calculate amount:
  - workday: jam 1 = 1.5x, jam 2+ = 2x
  - weekend/holiday: jam 1-7 = 2x, jam 8 = 3x, jam 9-10 = 4x
  - hourly_rate = monthly_salary / 173
- update overtime_requests.actual_hours, amount_calculated
        │
        ▼
[OPTION: COMPENSATORY LEAVE]
- karyawan pilih take CL alih-alih uang
- saldo CL bertambah, payroll skip overtime amount
        │
        ▼
[FLOW TO PAYROLL] (F05)
- payroll_period aggregate semua overtime_logs
- masuk sebagai earning component di payroll_item
```

---

## F05 — Monthly Payroll Cycle (FLOW UTAMA)

**Trigger:** Cut-off tanggal (e.g., 20 setiap bulan untuk payroll 25)

```
═════════════════════════════════════════════════════════════
                    H-7 sebelum payroll date
═════════════════════════════════════════════════════════════

[CUT-OFF ATTENDANCE] (M10)
- pukul 23:59 di cutoff_attendance_date
- semua attendance correction harus disubmit + approved
- semua overtime_request harus finalized
- HR notif: "Cut-off attendance closed. Submit corrections now."

[CUT-OFF REIMBURSE] (M18)
- semua reimbursement_request harus approved by H-7
- yang belum approved akan masuk payroll bulan berikutnya

[CUT-OFF LEAVE] (M12)
- semua leave_request finalized
- unpaid leave dihitung untuk deduct

[CUT-OFF LOAN] (M27)
- installment yang due bulan ini sudah locked

═════════════════════════════════════════════════════════════
                    H-3 PAYROLL RUN
═════════════════════════════════════════════════════════════

[HR TRIGGER PAYROLL RUN] (M14)
Action: HR Admin click "Run Payroll" di Filament
        │
        ▼
[CREATE PAYROLL_RUN, status=draft]
        │
        ▼
[QUEUE BACKGROUND JOB] (Horizon)
- chunk by 50 employees per job (parallel)
- update status='calculating'
        │
        ▼
[CALCULATE PER EMPLOYEE]
For each employee:
  1. Fetch master data: basic salary, allowances, position, company
  2. Fetch attendance (M10):
     - working_days, present_days, absent_days, leave_days (paid/unpaid)
  3. Fetch overtime (M11):
     - total_hours_workday, total_hours_holiday, amount
  4. Fetch reimbursement (M18) approved & not yet paid
  5. Fetch loan installments (M27) due this month
  6. Fetch cooperative savings (M27) auto-deduct
  
  7. Calculate components:
     - For each payroll_component (earning/deduction/benefit):
       - if fixed: amount = default
       - if formula: eval formula (e.g., `gaji_pokok * 0.05`)
       - if attendance_based: amount = unit_price * present_days
     - Apply prorate factor if mid-month join/resign
     - Sum total_earnings, total_deductions
  
  8. Calculate gross (taxable basis):
     - sum of taxable earnings
  
  9. Calculate BPJS (M16):
     - basis = sum of bpjs_basis components (capped)
     - kesehatan: 4% employer + 1% employee (cap 12jt)
     - JHT: 3.7% employer + 2% employee
     - JP: 2% employer + 1% employee (cap 10.042jt 2026)
     - JKK: per risk_level
     - JKM: 0.3% employer
     - Total employee deduction added to deductions
     - Total employer cost recorded (not deducted from employee)
  
  10. Calculate PPh 21 (M15):
      - method default: TER 2024
      - basis = gross (earnings) — pengurang (jabatan + BPJS karyawan)
      - PTKP based on employee.ptkp_status
      - lookup TER bracket based on PTKP category (A/B/C)
      - pph_21 = bracket_rate * gross_monthly
      - if THR/bonus: hitung pajak setahun − pajak setahun tanpa bonus
      - simpan tax_calculations
  
  11. Final net_pay:
      net = total_earnings − total_deductions − pph_21 − bpjs_employee_total
        ▼
[STORE PAYROLL_ITEM & PAYROLL_ITEM_COMPONENTS]
status='calculated'
        │
        ▼
[GENERATE PREVIEW PAYSLIPS]
PDF preview (watermark "DRAFT")
        ▼
[UPDATE PAYROLL_RUN]
- total_employees, total_gross, total_net, total_tax, etc.
- status='review'

═════════════════════════════════════════════════════════════
                    H-2 REVIEW
═════════════════════════════════════════════════════════════

[HR REVIEW]
- inspect anomalies (lonjakan net, pajak)
- inspect held items (employee diabsen suspect)
- bulk re-calculate jika perlu
- spot-fix individual items
        │
        ▼
[HR APPROVE] → payroll_run.status='approved'
- create payroll_approval row
        │
        ▼
[FINANCE APPROVE DISBURSEMENT]
- M14 + M17 trigger
- create disbursement_run
- generate bank batch file (BCA/Mandiri/etc.)
- OR instant disburse via Flip/Xendit
- finance review file
        │
        ▼ FINANCE APPROVE & EXECUTE
[DISBURSE] (M17)
- upload batch file to internet banking (manual step) OR call API
- update disbursement_items.status as confirmation received
- handle failures → retry queue

═════════════════════════════════════════════════════════════
                    PAYROLL DATE (e.g., 25)
═════════════════════════════════════════════════════════════

[PAYSLIP DELIVERY]
- finalize payslips (remove watermark)
- generate password-locked PDF (password = NIK or DOB)
- send email + WhatsApp + push to mobile app
- karyawan terima → download → buka pakai password
        │
        ▼
[PAYROLL_RUN.status='finalized']
[LOCK PAYROLL PERIOD]
- payroll_periods.status='paid'
- no more edit
        │
        ▼
[NEXT MONTH PREP]
- create new payroll_period
- carry-over balances reset jika perlu

═════════════════════════════════════════════════════════════
                    POST-PAYROLL
═════════════════════════════════════════════════════════════

[GENERATE TAX REPORT BULANAN] (M15)
- 1721-I generated → submit ke e-Bupot atau DJP API

[GENERATE BPJS SIPP] (M16)
- file SIPP generated → upload ke BPJSTK
- pay iuran ke bank

[ACCOUNTING SYNC] (Phase 6+, M18)
- post journal entry ke Accurate/Jurnal/Xero via adapter
- breakdown: HPP gaji + BPJS expense + PPh 21 hutang + Kas
```

**Idempotency:**
- Payroll re-run untuk period yang sama: replace existing payroll_items (after confirmation)
- Disbursement re-execution: skip already-completed items, retry failed

**Error handling:**
- Calculation error per employee → flag, continue other employees, alert HR
- Bank batch file format error → validate before download
- Failed disbursement → auto-retry 3x, then dead-letter, HR manual review

---

## F06 — Recruitment Flow

```
[HIRING MANAGER REQUEST]
- fill requisition (position, budget, urgency)
        │
        ▼
[HR APPROVE & CREATE JOB POSTING] (M19)
- write job description
- pilih channel (career page, LinkedIn, Jobstreet)
- publish → URL public
        │
        ▼
[CANDIDATE APPLY]
Mode A — Public career page:
- fill form + upload CV
- consent UU PDP

Mode B — Manual import:
- HR import CSV + CV files

Mode C — Referral:
- karyawan submit referral via ESS
- candidate auto-pre-filled
        │
        ▼
[AI SCREENING] (Phase 5+, M19)
- LLM analyze CV vs JD
- generate ai_score, ai_summary
- auto-stage: top 30% → "screened", rest → "applied"
        │
        ▼
[HR SHORTLIST]
- review top candidates manual
- move to "interview" stage
        │
        ▼
[INTERVIEW]
- schedule via M19 → Google Calendar invite
- multi-stage (phone → tech → HR → final)
- per interview: interview_feedback dari reviewer
- consolidate scorecard
        │
        ▼
[OFFER]
- generate offer letter (M04 template)
- e-sign request (M08)
- candidate accept/decline
- counter-offer → new offer revision
        │
        ▼ ACCEPTED
[CONVERT TO EMPLOYEE] (F01)
- create employees record (M01)
- create contract (M03)
- trigger onboarding (F14)
- close application + job_posting if filled
- referral bonus tracking (M19)
```

**1-click WA blast:**
- HR pilih candidates → "Send WhatsApp" → template + variables → kirim broadcast
- Track delivered/read/replied

---

## F07 — Reimbursement

```
[KARYAWAN SUBMIT EXPENSE] (M18)
- pilih kategori (transport/meals/medical/etc.)
- upload bukti foto/PDF
- isi nominal + tanggal + vendor
        │
        ▼
[OCR PROCESSING] (Phase 5+, optional)
- LLM Vision adapter extract: vendor, total, date
- HR/karyawan verify/edit
        │
        ▼
[VALIDATE POLICY] (M18)
- nominal <= max_per_request?
- monthly budget kategori dept masih cukup?
- duplicate detection?
        │
        ▼
[APPROVAL] (F12)
- routing by nominal:
  - <Rp 500k: manager only
  - Rp 500k-5jt: manager + HR
  - >5jt: manager + HR + Finance
        │
        ▼ APPROVED
[DISBURSEMENT METHOD]
Option A — Add to next payroll (default for small amounts):
- mark reimbursement_request.disbursement_method='payroll'
- M14 next payroll: include as earning component
- karyawan terima di slip gaji

Option B — Separate disbursement (urgent / large):
- create disbursement_item dengan amount
- execute via M17 (instant via Flip/Xendit OR batch)
- karyawan terima notif "transferred"
        │
        ▼
[BUDGET UPDATE] (M18)
- reimbursement_budgets.used_amount += amount
- alert HR jika budget dept melebihi 80%
```

**WA flow:**
```
Karyawan: [foto struk bensin]
Bot: "Saya deteksi:
  Vendor: Shell SPBU 31.111
  Total: Rp 250.000
  Tanggal: 21 Mei 2026
  Kategori: Transport
Lanjutkan submit?"
Karyawan: "Ya"
Bot: "✅ Submitted. Menunggu approval Manager Andi."
```

---

## F08 — Travel / SPPD

```
[KARYAWAN REQUEST PERJALANAN DINAS] (M26)
- isi: tujuan, tanggal, transportasi, estimasi biaya
        │
        ▼
[APPROVAL] (F12)
- manager → finance (if advance > Rp 5jt)
        │
        ▼ APPROVED
[GENERATE SPPD LETTER] (M04)
- nomor surat auto-increment
- isi variables (nama, jabatan, tujuan, tanggal)
- generate PDF → download
        │
        ▼
[CASH ADVANCE] (M26 + M17)
- finance disburse advance amount
- karyawan terima H-3
        │
        ▼
[KARYAWAN BERANGKAT]
- track real-time location (opsional)
- submit expense in-trip via mobile (F07 reimbursement)
        │
        ▼
[KEMBALI & SUBMIT SETTLEMENT] (M26)
- isi actual expenses, attach bukti
- balance: 
  - jika advance > actual: karyawan refund selisih
  - jika advance < actual: company bayar selisih
- per_diem auto-calc (rate per kota × hari)
        │
        ▼
[FINANCE VERIFY]
- approve settlement
- transfer selisih / minta refund
- close trip
```

---

## F09 — Loan / Pinjaman

```
[KARYAWAN APPLY LOAN] (M27)
- pilih jenis (cash advance / installment)
- isi: nominal, tenor, alasan
- upload bukti pendukung (jika required)
        │
        ▼
[POLICY VALIDATION] (M27)
- nominal <= max berdasarkan loan_policy (e.g., 3x gaji)
- tenure karyawan >= min_tenure_months
- belum ada outstanding loan jenis sama?
- DBR ratio (cicilan / gaji) <= 30%?
        │
        ▼
[APPROVAL] (F12)
- HR → Finance (CFO untuk nominal besar)
        │
        ▼ APPROVED
[DISBURSE] (M17)
- create loan record dengan tenor & installments
- generate installment schedule
- disburse principal ke karyawan
        │
        ▼
[MONTHLY DEDUCTION]
- F05 payroll cycle pick up loan_installments yang due
- auto-deduct dari net salary
- update installment.status='paid', loan.outstanding_balance
        │
        ▼ JIKA KARYAWAN RESIGN (F13)
[OUTSTANDING LOAN HANDLING]
- M28 exit flow check outstanding loan
- option: dipotong dari pesangon, atau bayar lump-sum
- jika tidak terbayar → write-off + boomerang tag
```

**Koperasi mode:**
```
[KARYAWAN JADI ANGGOTA] (M27)
- bayar simpanan pokok (one-time)
- bayar simpanan wajib (per bulan, auto-deduct)
- optional simpanan sukarela
        │
        ▼
[KARYAWAN BISA PINJAM]
- bunga lebih murah (e.g., 1%/bln) vs bank
- bunga jadi SHU dibagi anggota akhir tahun
        │
        ▼
[AKHIR TAHUN — SHU DISTRIBUTION]
- hitung total bunga + biaya admin
- bagi sesuai porsi simpanan + pemakaian
- distribusi via payroll bulan Desember atau Januari
```

---

## F10 — Performance Cycle

```
═════════════════════════════════════════════════════════════
                    QUARTER START (Q1, Q2, Q3, Q4)
═════════════════════════════════════════════════════════════

[OKR CYCLE OPEN] (M21)
- CEO set company OKR
- broadcast announcement (M06)
        │
        ▼
[CASCADE]
- C-level → VP → Director → Manager → IC
- setiap level set 3-5 objectives + 3-5 KR per objective
- alignment view (parent-child link)
        │
        ▼
[WEEKLY/MONTHLY CHECK-IN]
- karyawan update progress (% atau current_value)
- confidence_level (1-5)
- blocker
- manager 1-on-1 review

═════════════════════════════════════════════════════════════
                    QUARTER END
═════════════════════════════════════════════════════════════

[OKR REVIEW] (M21)
- score per objective (0.0 - 1.0)
- retrospective: lesson learned

═════════════════════════════════════════════════════════════
                    ANNUAL APPRAISAL (Akhir Tahun)
═════════════════════════════════════════════════════════════

[APPRAISAL CYCLE OPEN] (M22)
- HR set cycle: self → peer → manager → calibration
- assign forms per role
        │
        ▼
[SELF REVIEW]
- karyawan isi form (rating + open ended)
- linked dengan OKR score
        │
        ▼
[PEER REVIEW]
- karyawan minta peer feedback (3-5 peers)
- anonymous opsional
        │
        ▼
[MANAGER REVIEW]
- manager rating final + comments
- review goal achievement
        │
        ▼
[CALIBRATION MEETING]
- HR + leaders meet
- adjust ratings agar konsisten distribusi
- 9-box placement (M22)
- identify high-potential & low-performer
        │
        ▼
[FINALIZE]
- final_rating per karyawan
- update employee_competencies (M23)
- trigger:
  - salary increase recommendation
  - promotion candidate list
  - PIP (Performance Improvement Plan) untuk low performer
  - succession_plan update
        │
        ▼
[COMMUNICATE]
- 1-on-1 manager → karyawan
- karyawan view rating di ESS
- ack required
        │
        ▼
[ACTION ITEMS]
- IDP update (M23)
- enroll training (M24) untuk close gap
- salary adjustment di payroll bulan setelah cycle (M14)
```

---

## F11 — Learning Path

```
[NEW HIRE / ROLE CHANGE]
- system auto-assign mandatory training (M24)
  - security awareness
  - K3 safety
  - anti-bribery
  - role-specific (sales = product training, dll.)
        │
        ▼
[KARYAWAN ENROLL]
- buka course di mobile/web
- watch video, read PDF, take quiz
- progress tracked per module
        │
        ▼
[COMPLETE COURSE]
- pass quiz threshold
- generate certificate
- training_hours updated
- competency_level naik (M23)
        │
        ▼
[EXTERNAL TRAINING]
- karyawan submit attendance external training
- upload certificate
- biaya via reimburse (M18) atau separate budget
- training_hours + certificate updated
        │
        ▼
[COMPLIANCE REPORT]
- HR view: siapa belum lulus mandatory training
- escalate ke manager untuk follow-up
```

---

## F12 — Approval Workflow (Generic)

```
[REQUEST SUBMITTED]
- ANY request type (leave, OT, reimburse, travel, loan, asset, attendance correction)
        │
        ▼
[ROUTE DETERMINATION] (M05)
- match workflow berdasarkan: type + tenant + condition
- pilih workflow dengan priority tertinggi yang match
- ambil semua approval_workflow_steps in order
        │
        ▼
[CREATE APPROVAL_REQUEST]
- status='pending', current_step_order=1
        │
        ▼
[CREATE STEP 1 (approval_request_step)]
- resolve approver (employee/role/position/manager/dll.)
- send notification: push + email + WA
- start timeout countdown
        │
        ▼
APPROVER LOGS IN:
  ┌────────────────────────────────────────┐
  │ INBOX di Mobile/Web/WA                 │
  │ - swipe approve / reject               │
  │ - tap untuk lihat detail               │
  │ - comment + attachment opsional        │
  └────────────────────────────────────────┘
        │
        ├─ APPROVED:
        │   - step.status='approved', acted_at=now
        │   - if last step: approval_request.status='approved'
        │     trigger callback ke requestable (e.g., leave_request.status='approved')
        │   - else: advance to next step → create step 2 → notif next approver
        │
        ├─ REJECTED:
        │   - step.status='rejected', acted_at=now
        │   - approval_request.status='rejected'
        │   - callback: requestable.status='rejected', reason
        │   - notif requester
        │
        ├─ DELEGATED:
        │   - approver delegate ke orang lain
        │   - new step row dengan delegated_to_employee_id
        │
        └─ TIMEOUT (cron job tiap jam):
            - if timeout_hours passed and step.escalation_step_id is set:
              - auto-escalate ke step_id tsb
              - notif new approver
              - log "escalated due to timeout"
```

**Parallel approval:**
- step.approval_mode='all' = semua approver harus approve
- step.approval_mode='any' = salah satu cukup
- multi-approver: spawn multiple approval_request_step di order yang sama

---

## F13 — Resignation & Exit

```
[KARYAWAN SUBMIT RESIGN] (M28)
- exit_type (resignation/retirement/end_contract)
- notice_date, last_working_date (default H+30 untuk PKWTT)
- reason TEXT, reason_category
- voluntary BOOL
        │
        ▼
[APPROVAL] (F12)
- direct manager → HR Manager → CEO (opsional)
        │
        ▼ APPROVED
[CREATE CLEARANCE CHECKLIST] (M28)
- copy dari exit_clearance_template
- items:
  - IT: revoke email + SaaS access + return laptop
  - HR: collect ID card + complete exit interview
  - Finance: settle outstanding (loan, advance)
  - GA: return aset non-IT
  - Manager: knowledge handover document
        │
        ▼
[CLEARANCE EXECUTION] (paralel sebelum last day)
- assignees complete items satu per satu
- mark complete with proof
- HR monitor progress
        │
        ▼
[EXIT INTERVIEW] (M28)
- karyawan isi form (anonymous opsi)
- HR review (atau auto-generate insight)
- aggregate ke turnover analysis (M29)
        │
        ▼
[SEVERANCE CALCULATION] (M28 + UU CK)
- years_of_service
- uang_pesangon (sesuai tabel UU CK 6/2023)
- uang_penghargaan_masa_kerja
- uang_penggantian_hak (cuti belum dipakai, perumahan & kesehatan 15%)
- jika resign sukarela: hanya uang_penggantian_hak
- jika PHK: full package
        │
        ▼
[GENERATE PAKLARING] (M04)
- template paklaring → variables → PDF
- e-sign HR Manager
- delivered ke karyawan
        │
        ▼
[FINAL PAYMENT] (F05 off-cycle)
- payroll_run type='off_cycle'
- include:
  - prorate gaji bulan terakhir
  - severance
  - unused leave cash-out
  - THR prorate (jika dekat lebaran)
  - deduct: outstanding loan, equipment damage
- PPh 21 final (rate khusus untuk pesangon)
- BPJS final + claim JHT instruction
        │
        ▼
[ACCESS REVOCATION]
- IT execute revoke checklist
- super-admin disable user account
- archive employee data (retention)
        │
        ▼
[UPDATE EMPLOYEE STATUS]
- employees.employment_status='resigned'/'terminated'
- last_working_date set
- close all open requests
- close all assigned assets (M25)
        │
        ▼
[BOOMERANG TAG]
- HR set eligible_for_rehire (yes/no/case-by-case)
- saved untuk future referensi jika apply lagi
        │
        ▼
[POST-EXIT]
- update turnover_analytics
- AI insight (if Phase 5+): "resign reason: salary 40%, growth 30%, ..."
```

---

## F14 — Onboarding

```
[OFFER ACCEPTED → JOIN DATE APPROACH]
- HR enroll new hire ke onboarding_process (M20)
- pilih template (per role/dept)
- assign buddy
- assign manager
        │
        ▼
[PRE-JOIN (H-7 sampai H-1)]
- send welcome email + onboarding kit (link mobile app, ESS, calendar)
- collect dokumen via ESS upload
- IT prepare laptop + email account
- GA prepare ID card + meja
        │
        ▼
[DAY 1 — JOIN]
- new hire fingerprint enrollment / face capture (M10)
- e-sign kontrak (M08)
- handover laptop (M25)
- welcome lunch
- intro meeting with team
- mandatory training enrollment (M24): security, K3, anti-bribery
        │
        ▼
[WEEK 1]
- buddy 1-on-1 daily
- shadow team meeting
- complete security & K3 training
        │
        ▼
[MONTH 1 — 30-DAY REVIEW] (M20)
- manager + new hire 1-on-1
- check: settling in, expectations, blockers
- rating: on-track / needs-support / concern
- mid-probation check
        │
        ▼
[MONTH 2 — 60-DAY REVIEW]
- deeper performance check
- skill assessment vs role expectation (M23)
        │
        ▼
[MONTH 3 — 90-DAY PROBATION END REVIEW] (M20)
- formal probation review
- decision: confirm permanent | extend probation | terminate
        │
        ├─ CONFIRM:
        │   - update employees.employment_status='active'
        │   - generate SK pengangkatan tetap (M04)
        │   - karyawan resmi PKWTT (jika applicable)
        │
        ├─ EXTEND:
        │   - new probation_end_date + 1-2 bulan
        │   - PIP issued
        │
        └─ TERMINATE:
            - trigger F13 exit flow
```

---

## F15 — Tax Year-End (1721-A1)

```
[YEAR-END — DECEMBER]
        │
        ▼
[ACCUMULATE ANNUAL TAX] (M15)
- aggregate all monthly tax_calculations per employee
- annual gross, annual deduction, annual PPh 21
        │
        ▼
[RECONCILE & ADJUSTMENT]
- compare TER monthly accumulated vs actual annual progressive
- adjustment di payroll Desember:
  - lebih bayar → refund
  - kurang bayar → deduct
        │
        ▼
[GENERATE 1721-A1 PER KARYAWAN] (M15)
- PDF format DJP
- distribute ke karyawan via ESS (download)
- karyawan pakai untuk SPT Tahunan personal
        │
        ▼
[REPORT TAHUNAN KE DJP]
- submit 1721 via DJP API atau e-Bupot
- arsip semua bukti potong
```

---

## F16 — BPJS Monthly Reporting

```
[AKHIR BULAN PAYROLL] (F05 selesai)
        │
        ▼
[GENERATE SIPP FILE] (M16)
- format XLSX official BPJSTK
- isi: NIK, nama, salary basis, iuran per program
- export untuk upload manual ATAU direct API submit
        │
        ▼
[UPLOAD SIPP via BPJSTK website]
- HR login portal SIPP
- upload file → validate → submit
        │
        ▼
[PAY IURAN]
- generate VA (Virtual Account) BPJSTK
- transfer ke VA
- konfirmasi
        │
        ▼
[BPJS KESEHATAN]
- similar flow: generate file, upload Edabu portal, pay VA
        │
        ▼
[KARYAWAN AKSES BPJSTKU APP]
- lihat saldo JHT, klaim, dll.
- saldo update setelah pembayaran iuran ke BPJSTK
```

---

## F17 — Multi-Company Group Consolidation (Enterprise)

```
[GROUP STRUCTURE]
- parent company "Acme Group"
- subsidiary: Acme Manufacturing, Acme Retail, Acme Tech
        │
        ▼
[PER-COMPANY OPERATION]
- masing-masing punya: NPWP, NPP BPJS, kontrak, payroll terpisah
- legal entity terpisah
        │
        ▼
[GROUP HRD VIEW]
- super-HR dengan akses semua company
- dashboard consolidated headcount, cost, turnover
- transfer karyawan antar company (M02 mutation)
        │
        ▼
[INTERCOMPANY PAYROLL]
- payroll dihitung per company
- karyawan dipindah mid-bulan: prorate antar 2 company
- letter of transfer + final settlement company asal
        │
        ▼
[CONSOLIDATED REPORT] (M29)
- aggregate semua company
- breakdown per company + total group
- export untuk holding accounting
```

---

## Notification Matrix (siapa dapat notif kapan)

| Event | Karyawan | Manager | HR | Finance | C-level |
|---|---|---|---|---|---|
| Cuti submitted | ✅ (self) | ✅ | ⚪ | ⚪ | ⚪ |
| Cuti approved/rejected | ✅ | ⚪ | ✅ (HR-related) | ⚪ | ⚪ |
| Late clock-in | ✅ | ✅ (if >30min) | ✅ (aggregate report) | ⚪ | ⚪ |
| Overtime > policy | ⚪ | ✅ | ✅ | ⚪ | ⚪ |
| Payroll ready review | ⚪ | ⚪ | ✅ | ⚪ | ⚪ |
| Payroll disbursed | ✅ | ⚪ | ✅ | ✅ | ⚪ |
| Reimburse submitted | ✅ | ✅ | ⚪ | ⚪ (>5jt) | ⚪ |
| Contract expiring T-30 | ✅ | ✅ | ✅ | ⚪ | ⚪ |
| Birthday | ✅ team | ⚪ | ⚪ | ⚪ | ⚪ |
| New hire join | ⚪ team | ✅ | ✅ | ⚪ | ⚪ |
| Resignation | ⚪ | ✅ | ✅ | ✅ | ✅ (sr role) |
| Headcount alert | ⚪ | ⚪ | ✅ | ✅ | ✅ |
| Turnover spike (AI) | ⚪ | ⚪ | ✅ | ⚪ | ✅ |

---

## State Machines

### Leave Request
```
draft → submitted → [approval flow] → approved | rejected | canceled
                                          ↓
                                     in_progress (cuti berjalan)
                                          ↓
                                       completed
```

### Payroll Run
```
draft → calculating → review → approved → disbursing → finalized
              ↓           ↓                    ↓
            failed     held(some)          partially_completed
```

### Application (ATS)
```
applied → screening → interview → offer → hired (→ employee)
                ↓         ↓          ↓
            rejected ─ rejected ─ rejected/withdrawn
```

### Exit Request
```
pending → approved → in_clearance → completed
   ↓
withdrawn
```

---

## Failure & Recovery Patterns

### Payroll partial failure
- 480/500 employees calculated, 20 failed (e.g., missing data)
- payroll_run.status='partial', show 20 in "needs attention"
- HR fix data → re-run only failed 20
- merge back to run

### Disbursement bank rejection
- 5 out of 500 transfers rejected (e.g., invalid rekening)
- disbursement_items.status='failed' for those 5
- alert finance
- HR fix rekening number → manual transfer atau re-batch

### Attendance correction abuse
- karyawan request correction terlalu sering
- HR dashboard show pattern (>5 correction/bulan = flag)
- policy enforce limit (e.g., max 3 correction/bulan auto-approved, sisanya require manager justify)

### Approval workflow stuck
- approver tidak respond > 3 hari
- auto-escalation to skip-level
- alert HR to investigate

### License v3 fail
- license server unreachable: tolerance 30 hari offline mode
- license expired: read-only mode, no new payroll/data write
- license tampered: full block, alert support
