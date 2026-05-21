# 09 — Marketing Front Page Spec

> Per global rule (CLAUDE.md §3 — Marketing Front Page wajib di setiap project). Landing page produktif di route `/` dengan **real screenshots** dari aplikasi running.

## 1. Route Setup

```php
// routes/web.php
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('filament.admin.pages.dashboard');
    }
    return view('marketing.home');
})->name('home');
```

- **Guest** → marketing landing page
- **Authenticated** → redirect ke `/admin` (Filament panel)
- **Tenant subdomain** → kalau tenant-specific (acme.hris.app), tampilkan tenant-branded landing kalau di-set, default ke main landing
- **Custom domain** → tenant-branded landing wajib

## 2. Required Sections

### 2.1 Hero
- Headline value-prop bahasa Indonesia
- Sub-copy 1-2 kalimat
- 2 CTA:
  - Primary: "Coba Demo Gratis"
  - Secondary: "Lihat Tampilan" (scroll to screenshot gallery)
- Background: gradient subtle atau abstract pattern

**Copy draft:**
```
HRIS Indonesia yang Karyawan Pakai dari WhatsApp,
Owner Pilih Provider AI Sendiri, dan Bisa Beli Sekali Pakai Selamanya.

Payroll 5 menit. Compliance PPh 21 TER + BPJS otomatis update. 
BYOK provider — kamu kontrol AI, SMS, bank, e-sign sendiri.

[ Coba Demo Gratis ]    [ Lihat Tampilan ]
```

### 2.2 Trust Strip / Use Case Strip
3-5 persona dengan icon:
- 🏭 **Manufaktur** — shift rotasi + overtime kompleks
- 🍽 **F&B & Retail** — multi-outlet + part-time
- 🏗 **Konstruksi** — site-based + harian/borongan
- 💼 **Startup & Tech** — OKR + 360 review
- 🏥 **Klinik & Healthcare** — shift 24-7

### 2.3 Problem-Solution
Before/after card pairs:

| Before (Excel/Manual) | After (HRIS) |
|---|---|
| Payroll lembur 3 hari setiap akhir bulan | Payroll selesai 30 menit, otomatis taat PPh & BPJS |
| Cuti via email + WA chat — sering miss approve | Cuti via app, approver tinggal swipe, notif otomatis |
| Slip gaji kirim 1-1 via WA, pakai password manual | Slip gaji terkirim otomatis ke WA karyawan dengan password locked |
| Lupa update PPh 21 TER 2024 → keluar SP DJP | Update aturan pajak otomatis di sistem, audit-ready |
| Owner kunci di Talenta — switch berarti migrasi data 6 bulan | BYOK, switch provider AI/SMS/bank kapan saja tanpa migrasi |

### 2.4 Feature Sections (Alternating Layout, 8-10 Sections)

Setiap section punya:
- **Real screenshot** (Playwright capture)
- Headline + 1 paragraf benefit
- 4-6 bullet point fitur spesifik

**Section list:**

1. **Payroll yang Mengerti Indonesia**
   - Screenshot: Payroll run review page di Filament
   - Bullets: PPh 21 TER 2024 auto, BPJS 5 program, THR generator, bank batch BCA/Mandiri/BNI/BRI/CIMB, instant disburse Flip/Xendit, slip gaji password-protected

2. **Absensi Anti-Cheat dari Mobile + WhatsApp**
   - Screenshot: Mobile app clock-in screen + live tracking dashboard
   - Bullets: GPS + geofence, selfie + face recognition, mock GPS detection, offline mode, WhatsApp bot clock-in, fingerprint integration

3. **Cuti & Lembur 1-Tap di Mobile**
   - Screenshot: Leave request mobile + approval inbox manager
   - Bullets: Saldo per jenis, carry-over otomatis, Kepmenaker 102 overtime calc, multi-level approval, delegation saat manager cuti

4. **Recruitment dengan AI CV Screening**
   - Screenshot: Pipeline kanban + AI score per candidate
   - Bullets: Career page publik, LinkedIn import, AI match score, 1-click WA blast kandidat, e-sign offer letter

5. **OKR & 360° Performance Review**
   - Screenshot: OKR cascading + 9-box matrix
   - Bullets: Cascading unlimited, weekly check-in, 360 review, 9-box, AI summary feedback

6. **Letter Generator Sekali Klik**
   - Screenshot: SK template builder + generated PDF preview
   - Bullets: SK, paklaring, SP, mutasi, promosi; template variable merge; e-sign BSrE/Privy; arsip otomatis

7. **Reimbursement OCR via WhatsApp**
   - Screenshot: WA chat foto struk → auto-extract
   - Bullets: OCR receipt, budget tracking real-time, multi-currency, per-diem, corporate card reconciliation

8. **Bawa Provider AI Sendiri (BYOK)**
   - Screenshot: Settings > Integrations page
   - Bullets: OpenAI, DeepSeek, Groq, Ollama self-host — semua kompatibel; SMS bebas provider; bank disburse fleksibel; biaya transparan dari provider langsung

9. **Analytics dengan AI Narrative Insight**
   - Screenshot: Dashboard + AI narrative card
   - Bullets: Pre-built dashboard, custom report builder, predictive turnover risk, compensation benchmark, scheduled email

10. **License-Once: Beli Sekali, Pakai Selamanya**
    - Screenshot: License pairing wizard `/__pair`
    - Bullets: Self-host on-premise, data residency Indonesia, satu kali bayar, annual maintenance opsional, source code dilindungi license v3

### 2.5 Screenshot Gallery
Grid 6-9 halaman lain dengan caption singkat:
- Org chart visual
- Employee detail page (PII masked)
- Shift schedule calendar
- Payslip preview PDF
- Career page publik (tenant view)
- Mobile app dashboard
- WhatsApp bot conversation
- 1721-A1 form preview
- BPJS SIPP export

### 2.6 Use Cases (4 Persona Real)
Industry-specific cases dengan numbers:
1. **Pabrik garmen di Bekasi (350 karyawan)** — 3 shift rotasi, 5 cabang, payroll dulu 5 hari sekarang 4 jam
2. **Klinik kecantikan chain (60 karyawan)** — multi-outlet, komisi sales auto-calc, attendance fingerprint
3. **F&B chain (180 karyawan)** — split shift, tipping pool, hourly worker via WA
4. **Startup Tech (45 karyawan)** — OKR cascading, 360 review, BYOK OpenAI untuk CV screening

### 2.7 Demo Accounts Table
Tabel semua role + email + password + cakupan, dengan link "Login →":

| Role | Email | Password | Cakupan |
|---|---|---|---|
| Owner / Super Admin | owner@hris.test | password | Full akses semua modul |
| HR Admin | hr@hris.test | password | Employee, payroll, attendance, leave |
| HR Staff | hrstaff@hris.test | password | Limited HR (no payroll approve) |
| Finance | finance@hris.test | password | Payroll approve, disbursement, reimburse |
| Manager Sales | manager-sales@hris.test | password | Approve cuti/OT tim sales |
| Manager Tech | manager-tech@hris.test | password | Approve cuti/OT tim engineer |
| Employee Office | employee@hris.test | password | ESS personal scope |
| Employee Field (mobile-first) | field@hris.test | password | ESS mobile + WA bot |

CTA: [Login Demo →](/admin/login)

### 2.8 Pricing
3-4 tier dengan toggle bulanan/tahunan:

**Starter** — Rp 10.000/karyawan/bulan
- Up to 25 karyawan
- Modul: Employee, Attendance, Payroll, Tax, BPJS, Leave, ESS
- Mobile app (iOS + Android)
- Email support
- 14 hari trial

**Basic** — Rp 15.000/karyawan/bulan (Recommended)
- Up to 100 karyawan
- Semua Starter + Reimburse, Letter generator, Bank disburse, Multi-cabang
- WhatsApp bot ESS
- Email + chat support

**Essential** — Rp 25.000/karyawan/bulan
- Up to 500 karyawan
- Semua Basic + Recruitment, OKR, Appraisal 360, LMS, Asset, Travel, Loan, Exit, Analytics
- Dedicated account manager
- Phone support

**Suite / Enterprise** — Custom
- Unlimited karyawan
- Semua Essential + DJP/SIPP direct submit, AI insight, predictive analytics, compensation benchmark
- Multi-company / group consolidation
- Priority support + SLA 99.9%
- Custom integration

**License-Once (Self-host)** — Mulai Rp 75 juta sekali bayar
- On-premise di server Anda sendiri
- Source code dilindungi license v3 dari whitelabel.co.id
- Annual maintenance 20% dari license value
- Full ownership data
- Tanpa per-user fee

### 2.9 Final CTA Section
Full-width dark gradient:
```
═══════════════════════════════════════════════════
   Siap Stop Lembur Setiap Akhir Bulan?
   
   Coba demo 14 hari gratis. Tanpa kartu kredit. 
   Migrasi data dari Talenta/Gadjian/Excel kami bantu.
   
   [ Mulai Demo Gratis ]    [ Konsultasi WA ]
═══════════════════════════════════════════════════
```

### 2.10 Footer
- **Produk:** Fitur, Harga, Demo, Mobile App, License-once
- **Solusi:** Per industri (Manufaktur, F&B, Retail, Konstruksi, Klinik, Tech)
- **Resources:** Blog, Help Center, Webinar, Calculator PPh 21
- **Perusahaan:** About, Karir, Kontak, Partner
- **Legal:** Privacy, Terms, Cookies, UU PDP Compliance, DPA
- **Compliance badges:** UU PDP, ISO 27001 (claim), BSrE PSrE, BPJSTK partner
- **Social:** LinkedIn, Instagram, Twitter, YouTube

## 3. Real Screenshot Capture Pipeline

**WAJIB**: Screenshot **bukan** dari Figma / mockup CSS / placeholder. Harus dari aplikasi running.

### Setup Playwright (sekali per project)

```bash
# Install
npm install --save-dev playwright
npx playwright install chromium

# Folder
mkdir -p resources/marketing/screenshots
mkdir -p scripts/capture
```

### Capture script: `scripts/capture/take-screenshots.js`

```javascript
const { chromium } = require('playwright');
const path = require('path');

const BASE_URL = process.env.APP_URL || 'http://localhost:8000';
const OUT_DIR = path.resolve(__dirname, '../../resources/marketing/screenshots');

const screens = [
  // [name, url-path, viewport, login-as]
  ['payroll-run-review', '/admin/payroll-runs/1', {width:1440,height:900}, 'hr@hris.test'],
  ['attendance-live-tracking', '/admin/attendance-live', {width:1440,height:900}, 'hr@hris.test'],
  ['ats-kanban', '/admin/applications', {width:1440,height:900}, 'hr@hris.test'],
  ['okr-cascading', '/admin/okr-cycles/1', {width:1440,height:900}, 'owner@hris.test'],
  ['nine-box', '/admin/appraisal-cycles/1/9-box', {width:1440,height:900}, 'owner@hris.test'],
  ['org-chart', '/admin/org-chart', {width:1440,height:900}, 'hr@hris.test'],
  ['letter-template-editor', '/admin/hr-letter-templates/1/edit', {width:1440,height:900}, 'hr@hris.test'],
  ['integrations-settings', '/admin/integrations', {width:1440,height:900}, 'owner@hris.test'],
  ['analytics-dashboard', '/admin/analytics', {width:1440,height:900}, 'owner@hris.test'],
  ['license-pair-wizard', '/__pair', {width:1440,height:900}, null],
  // Mobile
  ['mobile-clockin', '/m/attendance/check-in', {width:390,height:844}, 'employee@hris.test'],
  ['mobile-leave-request', '/m/leave/new', {width:390,height:844}, 'employee@hris.test'],
  ['mobile-payslip', '/m/payslips/latest', {width:390,height:844}, 'employee@hris.test'],
];

(async () => {
  const browser = await chromium.launch();
  
  for (const [name, urlPath, viewport, loginAs] of screens) {
    const context = await browser.newContext({ viewport });
    const page = await context.newPage();
    
    if (loginAs) {
      await page.goto(`${BASE_URL}/admin/login`);
      await page.fill('input[name="email"]', loginAs);
      await page.fill('input[name="password"]', 'password');
      await page.click('button[type="submit"]');
      await page.waitForURL(/admin/);
    }
    
    await page.goto(`${BASE_URL}${urlPath}`);
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(800); // animations settle
    
    await page.screenshot({
      path: path.join(OUT_DIR, `${name}.png`),
      fullPage: false,
    });
    
    console.log(`✓ Captured: ${name}.png`);
    await context.close();
  }
  
  await browser.close();
})();
```

### Run capture

```bash
# Ensure demo data is seeded
php artisan migrate:fresh --seed

# Start app
php artisan serve --port=8000 &

# Capture
node scripts/capture/take-screenshots.js

# Optimize PNG → WebP for web
for f in resources/marketing/screenshots/*.png; do
  cwebp -q 85 "$f" -o "${f%.png}.webp"
done
```

### Mobile screenshots
- Browser viewport 390x844 (iPhone 14 Pro)
- Atau capture dari Flutter app via `flutter screenshot` + emulator
- Save di `resources/marketing/screenshots/mobile/`

### WhatsApp screenshots
- Capture dari simulator WA Business (atau staging account)
- Composite screenshot ke phone-mockup frame via Figma export → PNG

## 4. Blade Template Structure

```
resources/views/marketing/
├── home.blade.php              # main landing
├── partials/
│   ├── hero.blade.php
│   ├── trust-strip.blade.php
│   ├── problem-solution.blade.php
│   ├── feature-section.blade.php (component, called 10x)
│   ├── screenshot-gallery.blade.php
│   ├── use-cases.blade.php
│   ├── demo-accounts.blade.php
│   ├── pricing.blade.php
│   ├── final-cta.blade.php
│   └── footer.blade.php
└── layouts/
    └── marketing.blade.php
```

### Reusable feature section component

```blade
{{-- resources/views/marketing/partials/feature-section.blade.php --}}
@props(['title','description','screenshot','bullets','reversed' => false])

<section class="py-20 bg-{{ $reversed ? 'gray-50' : 'white' }}">
  <div class="max-w-7xl mx-auto px-6">
    <div class="grid md:grid-cols-2 gap-12 items-center {{ $reversed ? 'md:[direction:rtl]' : '' }}">
      <div class="{{ $reversed ? 'md:[direction:ltr]' : '' }}">
        <img src="{{ asset('marketing/screenshots/'.$screenshot) }}"
             alt="{{ $title }}"
             class="rounded-xl shadow-2xl ring-1 ring-gray-200" />
      </div>
      <div class="{{ $reversed ? 'md:[direction:ltr]' : '' }}">
        <h2 class="text-4xl font-bold tracking-tight">{{ $title }}</h2>
        <p class="mt-4 text-lg text-gray-600 leading-relaxed">{{ $description }}</p>
        <ul class="mt-6 space-y-3">
          @foreach ($bullets as $bullet)
            <li class="flex items-start gap-3">
              <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path d="M16.7 5.3a1 1 0 010 1.4l-7 7a1 1 0 01-1.4 0l-3-3a1 1 0 011.4-1.4l2.3 2.3 6.3-6.3a1 1 0 011.4 0z"/>
              </svg>
              <span class="text-gray-700">{{ $bullet }}</span>
            </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</section>
```

### Tailwind setup
- Tailwind CSS via Vite (Laravel default)
- Custom theme: brand color, font (Inter atau Plus Jakarta Sans untuk Indonesia)
- Dark mode opsional (Phase 6+ kalau ada bandwidth)
- Mobile-first responsive

## 5. Performance Requirements
- LCP <2.5s on 4G
- Image: WebP with PNG fallback, lazy-loaded below fold
- Font: subset Latin + Indonesian extended
- No render-blocking JS
- Critical CSS inlined
- HTTP/2 server push untuk hero image

## 6. SEO Integration (interaksi dengan pSEO doc 07)
- Marketing home `/` = root, separate dari pSEO routes
- Internal links: home → /best-hris-2026, /compare/hris-vs-talenta, dst.
- Schema.org: SoftwareApplication + Organization + AggregateRating (kalau ada testimonial)
- Open Graph image: capture dari hero section atau dedicated OG image

## 7. Testing
- Lighthouse score ≥ 90 (Performance, A11y, SEO)
- Test di:
  - Chrome desktop
  - Safari iOS
  - Chrome Android
  - Edge desktop
  - Firefox
- Network throttling: 4G slow
- Real device test (bukan cuma DevTools simulator)

## 8. Update Cadence
- Screenshot: re-capture **wajib** tiap minor release (UI berubah → screenshot harus update)
- Copy: review tiap quarter (positioning, feature highlight rotation)
- Pricing: update saat ada perubahan (announce 30 hari sebelum)
- Use case stories: tambah 1 cerita real per quarter (setelah Phase 6)

## 9. A/B Testing (Phase 6+ Growth)
- Hero headline variant
- CTA copy variant
- Pricing display (monthly default vs annual default)
- Feature order

Tools: Posthog atau Plausible (no-cookie option).

## 10. Marketing-Page Implementation Checklist

Saat develop di Phase 6:

- [ ] Setup Playwright + capture script
- [ ] Seed demo data lengkap (semua modul filled)
- [ ] Capture 10+ admin screenshots
- [ ] Capture 6+ mobile screenshots
- [ ] Compose WA conversation screenshots
- [ ] Buat Blade layout + partials
- [ ] Tailwind theme custom
- [ ] Hero copy final
- [ ] Pricing tier dengan toggle bulanan/tahunan
- [ ] Demo accounts table dengan login link
- [ ] Schema.org markup
- [ ] OG image
- [ ] Sitemap include `/`
- [ ] Lighthouse audit ≥90
- [ ] Mobile responsive test
- [ ] Cross-browser test
- [ ] Track CTA click (Posthog/Plausible event)

---

## 11. Sample Hero Copy Variants (untuk A/B test post-launch)

| Variant | Headline | Sub-copy |
|---|---|---|
| **V1 — Outcome** | Payroll 5 Menit. PPh + BPJS Otomatis. | HRIS Indonesia all-in-one. BYOK provider. License-once self-host tersedia. |
| **V2 — Pain** | Berhenti Lembur Tiap Akhir Bulan. | HRIS yang menghitung gaji, PPh 21 TER, BPJS, dan kirim slip via WhatsApp — otomatis. |
| **V3 — Differentiator** | HRIS yang Karyawanmu Pakai dari WhatsApp. | Clock-in, cuti, slip gaji — semua dari WA. Owner pilih AI provider sendiri. |
| **V4 — Trust** | Dipakai 350+ Perusahaan Indonesia. | Dari startup hingga pabrik. PPh 21 TER 2024 compliant. ISO 27001-ready. |

Start dengan V3 (differentiator-led).
