# 07 — Roadmap & Phased Delivery

> 6 phase, 13 minggu sampai launch publik. Setelahnya: 6 minggu growth iteration berikutnya.

## Roadmap Bird-Eye View

```
                      MVP READY                LAUNCH-READY              GROWTH
                          │                          │                       │
W1 ─ W2 ─ W3 ─ W4 ─ W5 ─ W6 ─ W7 ─ W8 ─ W9 ─ W10 ─ W11 ─ W12 ─ W13 ──── W14+
│   Phase 0+1 Foundation+MVP     │  P2 Pay+Dis │ P3 Talent │ P4 Adv │ P5 Mobile+AI │ P6 pSEO+Mktg
```

---

## Phase 0 — Foundation (Week 0, sedang berjalan)

**Status:** ✅ docs ini, COMPLETE setelah user approve.

**Deliverable:**
- Phase 0 docs (8 dokumen ini)
- FEATURES.md (sudah ada)
- Memory entry (sudah ada)

**Exit criteria:**
- User approve docs
- Open Questions di SCOPE §8 dijawab atau accept defaults

---

## Phase 1 — Foundation + MVP Core (Week 1–4)

### Goal
Single-tenant payroll yang taat compliance Indonesia. Karyawan bisa absen + cuti + lihat slip dari mobile.

### Week 1 — Scaffold & Infrastructure
**Deliverable:**
- Laravel 13 project scaffold
- MySQL setup + migration runner
- Filament 4 admin install
- Multi-tenant package (stancl/tenancy) — row-level scope default
- Spatie Permission + Activitylog
- Sanctum API token
- Queue Redis + Horizon dashboard
- License v3 drop-in (dari kit whitelabel.co.id)
- Folder structure per ARCHITECTURE.md §3
- Docker Compose untuk dev (PHP-FPM + Nginx + MySQL + Redis + MinIO)

**Migration & seeder (foundation):**
- `tenants`, `users`, `roles`, `permissions`
- `companies`, `branches`, `departments`, `divisions`, `job_titles`, `job_levels`, `positions`
- `holidays` (global master Indonesia 2026)
- `ptkp_rules`, `ter_rates`, `bpjs_rates` (master 2026)
- `bank_master` (BCA, Mandiri, BNI, BRI, CIMB, Permata, Danamon, dll.)
- `klu_codes`, `umk_master`
- demo company "Acme Indonesia" + 25 demo employees

**Test:**
- Filament login `owner@hris.test/password`
- License pairing `/__pair` works
- Demo data loaded

### Week 2 — Employee + Org + Contract
**Deliverable:**
- M01 Employee Database (semua 12 tables + Filament Resource)
- M02 Organization (org chart visual via Filament widget)
- M03 Contract (template builder + PDF generate via Browsershot)
- M07 ESS basic (Blade view: dashboard, profile, slip view placeholder)

**Filament Resources:**
- `EmployeeResource` (list, view, create, edit, bulk import Excel via Maatwebsite/Excel)
- `CompanyResource`, `BranchResource`, `DepartmentResource`, `PositionResource`
- `ContractResource` + template editor

**API:**
- `/api/v1/me` (profile)
- `/api/v1/employees` (CRUD untuk admin)
- `/api/v1/org-chart`

**Test:**
- HR add karyawan baru → assign position → kontrak generated PDF

### Week 3 — Attendance + Shift + Leave
**Deliverable:**
- M09 Shift & Schedule (Filament Resource + calendar widget)
- M10 Attendance (basic GPS + selfie via API)
- M11 Overtime (request form + Kepmenaker 102 calc)
- M12 Leave Management (saldo + accrual + request)
- M13 Holiday Calendar (consume global + custom override)
- M05 Approval Workflow Engine (rule + step + inbox)

**Mobile App (Flutter) start:**
- Project init, theme, auth
- Login dengan Sanctum token
- Dashboard
- Clock-in/out screen (GPS + selfie via camera plugin)
- Leave request form
- Offline cache via Hive

**Test:**
- Karyawan clock-in via mobile → record di DB
- Request cuti tahunan → manager approve di Filament inbox → saldo deducted

### Week 4 — Payroll + Tax + BPJS
**Deliverable:**
- M14 Payroll Engine (component, formula, run, calculation queue, payslip PDF)
- M15 Tax PPh 21 TER 2024 (full)
- M16 BPJS Kesehatan + Ketenagakerjaan
- Payroll cycle end-to-end testable

**Calculation library:**
- `PayrollCalculationService` orchestrator
- `PPh21TERCalculator` (sesuai PMK 168/2023)
- `BPJSCalculator` per program
- `OvertimeCalculator` Kepmenaker 102
- `ProrationCalculator` mid-month

**Filament Pages:**
- Payroll Run wizard (create → calculate → review → approve)
- Payslip preview
- Tax report 1721-A1 (placeholder, full form next phase)
- BPJS calculation preview

**Test:**
- Run payroll demo 25 employees → semua hitungan match Excel benchmark
- Generate slip gaji PDF → download

**Exit criteria Phase 1:**
- ✅ HR Admin bisa: input karyawan, atur shift, jalankan payroll
- ✅ Karyawan bisa: clock-in via mobile, request cuti, lihat slip
- ✅ Payroll output match dengan kalkulasi manual (validated by external accountant)
- ✅ Multi-tenant isolation tested (data tidak leak antar tenant)

---

## Phase 2 — Disbursement & Approvals (Week 5)

### Goal
Bayar gaji ke bank otomatis + reimbursement flow + letter generator.

**Deliverable:**
- M17 Bank Disbursement (batch file BCA, Mandiri, BNI, BRI, CIMB)
- M18 Reimbursement (request + policy + budget)
- M04 HR Letter Generator (template + PDF + variable merge)
- M06 Announcement (broadcast + ack)
- Approval workflow library siap pakai untuk semua request type

**Dynamic adapter:**
- `BatchFileAdapter` per bank format
- `BankAdapterConfig` admin UI di Filament
- Setup wizard saat tenant onboard

**Test:**
- HR generate bank batch file BCA → buka di KlikBCA → format valid
- Karyawan submit reimbursement → manager approve → masuk slip gaji bulan depan
- Generate SK pengangkatan via template → PDF muncul

---

## Phase 3 — Talent (Week 6–7)

### Goal
Recruitment + Onboarding + OKR + basic Appraisal jalan.

### Week 6 — Recruitment + Onboarding
**Deliverable:**
- M19 ATS (career page publik, application, pipeline kanban, interview scheduler)
- M20 Onboarding (template, checklist, probation review)
- Career page route public (subdomain `karir.{tenant}.hris.app`)

**Filament:**
- `JobPostingResource`, `CandidateResource`, `ApplicationResource`
- Kanban view (drag-drop stages) — Filament plugin atau custom Livewire
- `OnboardingTemplateResource`

**Public:**
- Career page Blade view (responsive, mobile-friendly)
- Apply form dengan CV upload
- UU PDP consent checkbox

**Test:**
- Buat job posting → publish → kandidat apply dari public link → masuk pipeline
- Hire kandidat → trigger onboarding template

### Week 7 — OKR + Appraisal
**Deliverable:**
- M21 OKR & KPI (cycle, objective, key result, check-in, cascading)
- M22 Performance Appraisal 360° (cycle, form builder, response, 9-box)

**Filament:**
- `OKRCycleResource`, `ObjectiveResource`
- Cascading view (tree)
- `AppraisalCycleResource`, `AppraisalFormResource`
- 9-box visualization widget

**Test:**
- CEO set company OKR → cascade ke departments → IC check-in weekly
- Run mini appraisal cycle → 360 review → final rating

---

## Phase 4 — Advanced Modules (Week 8–9)

### Goal
LMS + Asset + Travel + Loan + Exit + Analytics dasar.

### Week 8 — LMS + Asset + Travel
**Deliverable:**
- M24 LMS (course, module, quiz, certificate, enrollment)
- M25 Asset (register, assign, return)
- M26 Travel/SPPD (request, advance, settlement, per-diem)
- M23 Competency (skill matrix + assessment)

**Test:**
- Upload course (video + quiz) → karyawan enroll → complete → certificate
- Assign laptop ke karyawan baru → return saat resign
- Travel request → SPPD letter generated → cash advance disburse → settlement

### Week 9 — Loan + Exit + Analytics
**Deliverable:**
- M27 Loan (request, installment, auto-deduct payroll, koperasi mode)
- M28 Exit (resignation, clearance, exit interview, severance, paklaring, access revocation)
- M29 Analytics (dashboard pre-built + custom report builder basic)
- M08 Digital Signature (BSrE/Privy adapter)

**Filament:**
- Loan policy + request + installment view
- Exit wizard (resignation → clearance → settlement)
- Analytics dashboard widgets per modul
- Report builder (basic — pilih table, columns, filter)

**Test:**
- Karyawan ajukan loan → approved → auto-deduct dari payroll 6 bulan
- Karyawan resign → clearance → severance UU CK calculated → paklaring generated
- Dashboard analytics show metric per modul

**Exit criteria Phase 4:**
- ✅ Semua 29 modul functional
- ✅ Single-tenant SaaS demo siap dipajang
- ✅ Documentation per modul updated

---

## Phase 5 — Mobile + Differentiators (Week 10–12)

### Goal
Mobile app full-feature + WhatsApp bot + AI features + face recognition + DJP/SIPP integration.

### Week 10 — Mobile App Full
**Deliverable:**
- Flutter app fitur lengkap (semua 29 modul yang relevant)
- iOS + Android build + submit ke App Store + Play Store (beta)
- Push notification end-to-end (FCM)
- Offline-first untuk attendance + slip download
- Biometric login (fingerprint/face)

**Test:**
- Karyawan demo install app → semua fitur jalan
- Offline clock-in saat tidak ada sinyal → sync saat online

### Week 11 — WhatsApp Bot + AI
**Deliverable:**
- WhatsApp Business API adapter (Meta Official + Wati + Fonnte alternatives)
- WA bot full ESS:
  - clock-in (share location)
  - leave request natural language
  - lihat slip (password-protected PDF)
  - approve request (reply "OK" / "REJECT")
  - announcement notif
  - reimburse submit (kirim foto struk)
- LLM adapter (OpenAI compatible) untuk:
  - AI CV screening (M19)
  - OCR receipt (M18)
  - AI narrative summary di analytics (M29)
  - AI quiz generator (M24)

**Test:**
- Karyawan demo absen via WA → record sukses
- HR demo upload 20 CV → AI screening generate score
- Reimburse foto struk → OCR extract nominal

### Week 12 — Face Recognition + DJP/SIPP API
**Deliverable:**
- Face recognition + anti-spoofing (Google ML Kit + custom liveness)
- Mock GPS detection (cek device sensor)
- Bank account validation API integration (bila vendor tersedia)
- DJP Coretax API stub (siap live saat API available)
- BPJSTK SIPP direct submit (jika API tersedia, else file export tetap)
- Klaim JKK/JKM workflow

**Test:**
- Anti-spoofing detect foto-of-foto vs wajah asli
- Mock GPS detected → flag attendance

**Exit criteria Phase 5:**
- ✅ Semua tier-1 differentiator dari FEATURES.md live
- ✅ Mobile app rating ≥4.5 di beta tester
- ✅ WA bot success rate ≥95% transaksi tanpa error

---

## Phase 6 — pSEO + Marketing + Launch (Week 13)

### Goal
Website public + pSEO routes + pricing page + onboarding flow self-serve untuk public launch.

**Deliverable:**

### pSEO routes (per CLAUDE.md global rule)
1. `/best-aplikasi-hris-2026` (annual update)
2. `/best-aplikasi-hris-untuk-{industri}` (10 industri: manufaktur, retail, F&B, hospitality, startup, klinik, kontraktor, sekolah, koperasi, kontraktor)
3. `/compare/{a}-vs-{b}` (matrix: kita vs talenta/gadjian/sleekr/linovhr/broadwayshr/kantorku/karyaone)
4. `/alternatives-to-{competitor}` (7 competitor)
5. `/aplikasi-hris-{kota}` (40 kota: jakarta, surabaya, bandung, medan, semarang, makassar, palembang, batam, pekanbaru, dst.)
6. `/aplikasi-payroll-{kota}` (40 kota)
7. `/aplikasi-absensi-{industri}` (10 industri)

**Total estimated pages:** ~250+ unique pages, semua dengan:
- 300+ kata konten bermakna (generated dari template + DB data)
- JSON-LD schema (ItemList, FAQPage, Product, LocalBusiness)
- Meta tags lengkap (title, description, canonical, og:*, twitter:*)
- Dynamic sitemap.xml + robots.txt
- pretty URL with proper hyphens

### Public website (per docs/09-MARKETING-FRONT-PAGE.md)
- Marketing landing page di `/` dengan 10 section wajib (hero, trust strip, problem-solution, 10 feature sections alternating, screenshot gallery, use cases, demo accounts table, pricing, final CTA, footer)
- **Real screenshots via Playwright** capture dari aplikasi running (BUKAN mockup) — 10+ admin screenshots + 6+ mobile screenshots
- Pricing page (4 tier SaaS + license-once)
- Demo request form
- Blog scaffold (untuk content marketing)
- Customer story page
- About + contact + privacy + terms
- Lighthouse score ≥90 (Performance, A11y, SEO)

### Self-serve onboarding
- Sign-up → tenant provisioning auto
- Setup wizard (company profile, first karyawan, first payroll demo)
- Free trial 14 hari
- Stripe / Midtrans payment integration untuk upgrade
- License v3 SaaS mode validation

### Marketing assets
- Logo + brand kit
- Demo video (1-min features overview)
- Case study template (siapkan 3 dummy untuk launch)
- LinkedIn + Instagram + Twitter accounts

**Test:**
- Visitor di Google search "best hris jakarta 2026" → arrive di pSEO page
- Sign-up → tenant aktif dalam 30 detik
- Run demo payroll dalam onboarding wizard

**Exit criteria Phase 6:**
- ✅ Production live di hris.app (atau brand domain)
- ✅ 250+ pSEO pages indexed by Google
- ✅ First paying customer
- ✅ Press kit ready

---

## Post-Launch (Week 14+)

### Growth Iteration
- A/B test landing & pricing page
- Customer feedback loop → backlog prioritization
- Content marketing (blog SEO posts mingguan)
- Webinar / demo session
- Partner reseller program launch

### Quality & Hardening
- Pen-test third party (Q3)
- ISO 27001 prep (Q4)
- Load testing 10k karyawan / tenant
- Disaster recovery drill

### Roadmap Year 2 (hint)
- Accounting integration depth (Accurate, Jurnal, Xero) untuk auto-journal
- ESS chatbot AI (general HR Q&A)
- Workforce planning + budget simulation
- Compensation benchmark via data partner (Mercer/Kelly)
- Marketplace add-on (background check, BG verify, drug test)
- White-label reseller portal
- API public for third-party integrators
- Multi-language (English, Mandarin Simplified)

---

## Resource Estimate

### Phase 1 (4 weeks)
- 1 backend dev full-time
- 0.5 mobile dev (Flutter)
- 0.5 DevOps (setup infra)

### Phase 2-3 (3 weeks)
- 1 backend
- 0.5 mobile

### Phase 4 (2 weeks)
- 1 backend
- 0.5 mobile

### Phase 5 (3 weeks)
- 1 backend
- 1 mobile full-time
- 0.5 AI/ML engineer

### Phase 6 (1 week)
- 1 backend
- 0.5 frontend (pSEO + landing)
- 1 marketing/content

**Total effort:** ~18 person-weeks (bisa di-compress dengan tim lebih besar)

## Risk Register

| Risk | Likelihood | Impact | Mitigation | Owner |
|---|---|---|---|---|
| PPh 21 TER calculation salah | Medium | Critical | Pair dengan accountant validate; benchmark dataset wajib | Tech Lead |
| Bank batch file format reject | High | High | Test setiap bank dengan dummy transfer Rp 1 dulu | DevOps |
| Mobile app rejected App Store | Medium | High | Privacy policy + UU PDP compliance ditulis jelas | Mobile Dev |
| WA Business API account banned | Medium | Critical | Multi-vendor adapter (Wati, Fonnte, Maytapi fallback) | Tech Lead |
| LLM cost runaway (AI screening) | Medium | Medium | Owner BYOK = mereka kontrol cost; default low-cost model | Product |
| License v3 cracked | Low | High | Server validation + tamper detection + license_audit_log | Security |
| pSEO over-promise konten | Medium | Medium | Generate hanya kalau ada data nyata; pakai DB content (review excerpts, dll.) | Content |
| Multi-tenant data leak bug | Low | Critical | Wajib test isolation + automated tenant-bleed test di CI | QA |
| Payroll day server down | Low | Critical | Multi-region failover + queue async (job tetap selesai meski server reboot) | DevOps |
| Compliance change mid-flight | Medium | Medium | Modular formula + admin-editable rate table | Tech Lead |

## Milestones (Key Dates dari hari ini 2026-05-21)

| Milestone | Target Date |
|---|---|
| Phase 0 docs approved | 2026-05-22 |
| Phase 1 MVP ready | 2026-06-20 |
| Phase 2 disbursement live | 2026-06-27 |
| Phase 3 talent modules done | 2026-07-11 |
| Phase 4 advanced complete | 2026-07-25 |
| Phase 5 mobile + AI + WA live | 2026-08-15 |
| Phase 6 public launch | 2026-08-22 |
| First paying customer | 2026-09-01 |
| 10 paying customers | 2026-10-15 |
| 100 paying customers | 2026-12-31 |
| ARR Rp 1M | 2027-03-31 |
| Break-even | 2027-05-20 |

## Decision Gates

Sebelum setiap phase berikutnya, review:
1. **Phase 1 exit:** Apakah payroll output bisa di-trust 100%? (Validated by accountant)
2. **Phase 2 exit:** Apakah bank disburse berfungsi di semua 5 bank target?
3. **Phase 3 exit:** Apakah cycle OKR + appraisal usable tanpa training berat?
4. **Phase 4 exit:** Apakah semua 29 modul functional, atau ada yang harus deprioritize?
5. **Phase 5 exit:** Apakah WA bot + face recognition stabil di production?
6. **Phase 6 exit:** Apakah ada inbound demo request dari pSEO traffic?

Jika gate gagal: **re-prioritize, jangan force schedule.** Quality > timeline.
