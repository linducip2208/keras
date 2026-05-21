# 08 — Dynamic Integration Spec

> Mengikuti CLAUDE.md §2 (No Hardcoded Providers). Semua integrasi pihak ketiga **dinamis via DB config + format-based adapter**. Owner perusahaan bawa key sendiri (BYOK).
>
> Reference: `D:\project laravel\foodscan\docs\10-AI-PROVIDERS.md`

## 1. Core Pattern

### Domain Categories di HRIS
1. **Bank Disbursement** — bayar gaji ke rekening
2. **E-Signature** — tanda tangan elektronik kontrak/surat
3. **WhatsApp Business** — bot ESS + notification
4. **LLM AI** — CV screening, OCR receipt, narrative insight, quiz generator
5. **OCR / Vision** — bukti reimburse, KTP scan
6. **Mail** — email notification & slip gaji
7. **SMS** — OTP, urgent notification
8. **Push Notification** — mobile FCM/APNs
9. **Storage** — file storage S3-compatible
10. **Accounting** — auto-journal ke accounting software
11. **HR Government** — DJP Coretax, BPJSTK SIPP
12. **Identity / KYC** — bank account validation, background check
13. **Calendar** — Google Calendar, Outlook Calendar
14. **Job Board** — LinkedIn, Jobstreet, Glints (ATS posting)
15. **Travel** — Traveloka Biz, agoda business (Phase 6+)
16. **Payment** — Stripe/Midtrans untuk SaaS subscription billing (tenant-side, bukan owner)

### Generic Architecture

```
                    ┌──────────────────────────────────┐
                    │   Adapter Registry (config-driven)│
                    │   reads: adapter_configs table   │
                    └────────────────┬─────────────────┘
                                     │
                ┌────────────────────┼─────────────────────┐
                │                    │                     │
                ▼                    ▼                     ▼
        ┌──────────────┐   ┌──────────────┐    ┌──────────────┐
        │ BankAdapter  │   │ESignAdapter  │    │  LLMAdapter  │
        │  contract    │   │  contract    │    │  contract    │
        └──────┬───────┘   └──────┬───────┘    └──────┬───────┘
               │                  │                    │
       ┌───────┴────────┐ ┌──────┴───────┐    ┌───────┴────────────┐
       │ BatchFile      │ │ BSrEFormat   │    │ OpenAICompatible   │
       │ RestApi        │ │ PrivyFormat  │    │ AnthropicFormat    │
       │ Redirect       │ │ GenericPKI   │    │ GeminiFormat       │
       └────────────────┘ └──────────────┘    └────────────────────┘
       (format, bukan vendor)
```

### Adapter Contract (PHP interface)

```php
namespace App\Adapters\Contracts;

interface AdapterInterface
{
    public function configure(array $config): void;
    public function test(): TestResult;     // verify connection
    public function execute(array $payload): ExecutionResult;
    public function logUsage(array $metadata): void;
}
```

Setiap domain punya interface lebih spesifik (extends `AdapterInterface`):
- `BankAdapterInterface` — `disburse(array $items): DisbursementResult`
- `ESignAdapterInterface` — `requestSign(array $signers): SignRequestResult`
- `WhatsAppAdapterInterface` — `sendMessage(string $to, string $body): SendResult`
- `LLMAdapterInterface` — `complete(array $messages): CompletionResult`
- `OCRAdapterInterface` — `extractReceipt(string $imageUrl): OCRResult`
- dst.

### DB Schema (generic)

```sql
CREATE TABLE adapter_configs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tenant_id BIGINT NOT NULL,
    domain VARCHAR(50) NOT NULL,         -- 'bank', 'esign', 'whatsapp', 'llm', 'ocr', 'mail', 'sms', 'storage', 'accounting'
    name VARCHAR(100) NOT NULL,           -- user-defined: "BCA Production", "OpenAI GPT-4", etc.
    format VARCHAR(50) NOT NULL,          -- 'batch_file_bca', 'openai_compatible', 'rest_api', 'bsre', etc.
    base_url VARCHAR(255),
    api_key_encrypted TEXT,
    api_secret_encrypted TEXT,
    extra_headers JSON,
    extra_config JSON,
    is_active BOOLEAN DEFAULT TRUE,
    is_test_mode BOOLEAN DEFAULT FALSE,
    last_tested_at TIMESTAMP,
    last_test_status VARCHAR(20),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX (tenant_id, domain),
    INDEX (tenant_id, format)
);
```

### Per-feature Picker

Setiap fitur yang butuh provider punya **picker** (dropdown) untuk pilih dari `adapter_configs` yang sudah ditambah owner:

```php
// di Filament Resource (contoh Reimbursement Settings)
Forms\Components\Select::make('ocr_adapter_id')
    ->relationship('ocrAdapter', 'name', fn ($q) => $q->where('domain', 'ocr')->where('is_active', true))
    ->label('OCR Provider')
    ->helperText('Pilih provider OCR yang sudah Anda tambahkan. Belum ada? Tambahkan di Settings > Integrations.')
    ->nullable();
```

Tidak pernah ada hardcoded default (e.g., "if no provider, use OpenAI"). Empty = fitur tidak aktif.

---

## 2. Per-Domain Spec

### 2.1 Bank Disbursement

**Use case:** Pay payroll ke rekening karyawan.

**Formats:**
| Format slug | Bank | Method |
|---|---|---|
| `batch_file_bca` | BCA | CSV/TXT upload via KlikBCA Bisnis |
| `batch_file_mandiri` | Mandiri | TXT upload via Cash Management |
| `batch_file_bni` | BNI | XLSX upload via BNI Direct |
| `batch_file_bri` | BRI | CSV upload via CMS |
| `batch_file_cimb` | CIMB | CSV upload via BizChannel |
| `batch_file_permata` | Permata | TXT upload via PermataNet |
| `batch_file_danamon` | Danamon | CSV upload |
| `rest_api_flip` | Flip | REST API instant |
| `rest_api_xendit` | Xendit | REST API instant |
| `rest_api_doku` | Doku | REST API instant |
| `rest_api_oy` | OY Indonesia | REST API instant |

**Owner config:**
- Untuk batch: bank profile (account number, company code, format version)
- Untuk API: API key, callback URL, mode (sandbox/production)

**Auto-fetch convenience:**
- "Test koneksi" button → ping balance/list endpoint
- "Validate sample employee account" → quick check 1 nomor rekening

**Reference:** `app/Adapters/Bank/`

### 2.2 E-Signature

**Use case:** Sign kontrak, SK, paklaring, offer letter.

**Formats:**
| Format slug | Provider | Notes |
|---|---|---|
| `bsre_format` | BSrE (BSSN) | PSrE Indonesia, legal |
| `privy_format` | Privy ID | Popular Indonesia |
| `digisign_format` | DigiSign | Alternative |
| `tekenaja_format` | TekenAja | Alternative |
| `generic_pki` | Custom PKI | Self-managed certificate |

**Owner config:**
- Account ID, API key, certificate identifier
- Verification method default (email/SMS/in-app OTP)

**Compliance:**
- UU ITE / UU PDP: e-signature dari PSrE terdaftar = legally binding
- Audit trail wajib: IP, timestamp, device, signer identity

### 2.3 WhatsApp Business

**Use case:** Bot ESS + announcement + payslip delivery + approval workflow.

**Formats:**
| Format slug | Vendor | Notes |
|---|---|---|
| `wa_official_meta` | Meta Business API | Direct, paling stabil, butuh verifikasi |
| `wa_cloud_360` | 360dialog Cloud | Reseller resmi Meta |
| `wa_rest_wati` | Wati | Popular, REST API |
| `wa_rest_fonnte` | Fonnte | Indonesia, murah |
| `wa_rest_maytapi` | Maytapi | Multi-device |
| `wa_rest_chatwoot` | Chatwoot self-host | OSS option |

**Owner config:**
- Phone number ID, access token, webhook secret
- Template message IDs (untuk Meta Official wajib approved template)

**Bot flow contract (sama untuk semua adapter):**
- Inbound webhook → standardize ke event `wa.message.received`
- Outbound `sendText`, `sendMedia`, `sendTemplate`, `sendInteractive` (buttons/list)
- Session management per phone number

**Reference:** `app/Adapters/WhatsApp/`

### 2.4 LLM AI

**Use case:**
- AI CV screening (M19)
- OCR receipt parsing (M18)
- Narrative insight di analytics (M29)
- Quiz generation dari course material (M24)
- 360 feedback summary (M22)
- HR chatbot (Year 2)

**Formats:**
| Format slug | Compatible with |
|---|---|
| `openai_compatible` | OpenAI, DeepSeek, Groq, Mistral, Together, Fireworks, OpenRouter, xAI, Cerebras, Anyscale, Ollama, LM Studio, vLLM, LiteLLM proxy, dll. |
| `anthropic_format` | Anthropic Claude direct |
| `gemini_format` | Google Gemini |
| `azure_openai_format` | Azure-hosted OpenAI |
| `image_generic` | Any image generation (DALL-E, Stable Diffusion, Imagen) |

**Owner config:**
- Base URL (e.g., `https://api.openai.com/v1`, `http://localhost:11434/v1`)
- API key
- Default model untuk feature ini (text input bebas: "gpt-4o-mini", "deepseek-chat", dll.)
- Max tokens, temperature defaults

**Per-feature dropdown:**
```
[CV Screening Settings]
  LLM Provider: [▼ pilih dari list adapter LLM yang sudah ditambahkan]
  Model: gpt-4o-mini  (text input, bukan dropdown — owner ketik manual)
  System Prompt: [editable template]
  Cost per 1k tokens: Rp 25  (owner input untuk tracking)
```

**Auto-fetch model list (optional):**
- Tombol "Fetch from /v1/models" → auto-populate model suggestions (tetap text input, hanya autosuggest)

**Reference:** `D:\project laravel\foodscan\docs\10-AI-PROVIDERS.md`

### 2.5 OCR / Vision

**Use case:** Extract data dari foto struk reimburse, KTP scan, dokumen.

**Formats:**
| Format slug | Compatible |
|---|---|
| `vision_llm_openai_compatible` | LLM with vision (GPT-4 Vision, Claude vision, Gemini vision) |
| `rest_textract` | AWS Textract |
| `rest_mindee` | Mindee receipt parser |
| `rest_nanonets` | Nanonets |
| `rest_generic_ocr` | Custom OCR API |

**Behavior:**
- Input: image URL atau base64
- Output: structured JSON sesuai schema (vendor, total, date, items)
- Confidence score
- Raw text fallback

### 2.6 Mail

**Use case:** Email payslip, notifikasi, password reset.

**Formats:**
| Format slug | Vendor |
|---|---|
| `smtp_generic` | Any SMTP |
| `rest_mailgun` | Mailgun |
| `rest_sendgrid` | SendGrid |
| `rest_ses` | AWS SES |
| `rest_postmark` | Postmark |
| `rest_brevo` | Brevo (formerly Sendinblue) |
| `rest_resend` | Resend |

**Owner config:**
- SMTP host/port/auth OR API key
- From email + name
- Reply-to

### 2.7 SMS

**Use case:** OTP login mobile, urgent notification (payroll delay, attendance miss).

**Formats:**
| Format slug | Vendor |
|---|---|
| `rest_twilio` | Twilio |
| `rest_vonage` | Vonage |
| `rest_zenziva` | Zenziva (Indonesia) |
| `rest_jatismobile` | Jatis Mobile |
| `rest_wavecell` | Wavecell |
| `smpp_generic` | Any SMPP-format gateway |

### 2.8 Push Notification

**Use case:** Mobile app notifications.

**Formats:**
| Format slug | Vendor |
|---|---|
| `fcm_v1` | Firebase Cloud Messaging |
| `apns_token` | Apple Push (iOS) |
| `onesignal_rest` | OneSignal |

### 2.9 Storage

**Use case:** Semua file upload (dokumen, foto absen, slip gaji PDF, sertifikat, attachment).

**Formats:**
| Format slug | Vendor |
|---|---|
| `s3_compatible` | AWS S3, Wasabi, Backblaze B2, Cloudflare R2, DigitalOcean Spaces, MinIO (self-host) — semua pakai S3 API |
| `gcs_native` | Google Cloud Storage (Phase 6+) |
| `azure_blob` | Azure Blob (Phase 6+) |

**Owner config:**
- Endpoint, region, bucket
- Access key, secret
- Public read URL pattern

**Default:** MinIO self-hosted (gratis untuk on-premise) atau Wasabi (cheap untuk SaaS).

### 2.10 Accounting (Phase 6+)

**Use case:** Auto-journal payroll cost ke accounting software.

**Formats:**
| Format slug | Vendor |
|---|---|
| `rest_accurate` | Accurate Online API |
| `rest_jurnal` | Jurnal by Mekari API |
| `rest_xero` | Xero |
| `rest_quickbooks` | QuickBooks |
| `rest_odoo` | Odoo |
| `webhook_outbound` | Generic webhook (untuk custom accounting) |

**Mapping config:**
- Account chart per komponen gaji
- Cost center mapping
- Tax account
- BPJS account

### 2.11 HR Government

#### 2.11.1 DJP Coretax (saat live 2026)
- Direct submit 1721-I bulanan + 1721-A1 tahunan
- Real-time validation NPWP & PTKP
- E-Bupot file generation

#### 2.11.2 BPJSTK SIPP
- File export XLSX (default, manual upload by HR)
- Direct API submit (jika BPJS open API tersedia)

#### 2.11.3 Indonesia Tax Helper
- KLU code lookup
- NPWP validation
- NIK validation (untuk dependents PTKP)

### 2.12 Identity / KYC

**Use case:**
- Bank account validation (cek nomor rekening valid + nama match)
- Background check (Phase 6+)

**Formats:**
| Format slug | Vendor |
|---|---|
| `rest_flip_validation` | Flip account validator |
| `rest_xendit_validation` | Xendit account validator |
| `rest_bri_api` | BRI account inquiry API |
| `rest_generic_kyc` | Custom KYC partner |

### 2.13 Calendar

**Use case:** Interview scheduler sync, leave sync, training session sync.

**Formats:**
| Format slug | Vendor |
|---|---|
| `caldav_generic` | Any CalDAV |
| `rest_google_calendar` | Google Calendar |
| `rest_microsoft_graph` | Outlook Calendar |
| `ical_export` | iCal file export (no auth) |

### 2.14 Job Board (M19 ATS)

**Use case:** Distribute job posting ke channel external.

**Formats:**
| Format slug | Vendor |
|---|---|
| `rest_linkedin_ats` | LinkedIn ATS Connect |
| `rest_jobstreet` | Jobstreet |
| `rest_glints` | Glints |
| `webhook_outbound` | Custom job board |

### 2.15 Travel (Phase 6+)

**Use case:** Book flight + hotel + train untuk M26 SPPD dari dalam app.

**Formats:**
- `rest_traveloka_biz`
- `rest_agoda_business`
- `rest_pegipegi`

### 2.16 Payment (untuk tenant billing — SaaS subscription)

**Use case:** Tenant bayar subscription bulanan ke kita.

> Catatan: ini provider yang **kita pilih sebagai operator SaaS**, bukan integrasi tenant. Tetapi tetap pakai format-based adapter agar kita bisa switch vendor tanpa rewrite.

**Formats:**
- `redirect_based` (Midtrans Snap, Xendit Invoice, Doku Checkout, Flip checkout)
- `embed_based` (Midtrans Embed, Stripe Elements)
- `qr_based` (QRIS direct)
- `bank_transfer_manual` (rekening kita + admin konfirmasi)

---

## 3. Onboarding Flow untuk Owner

Saat tenant baru sign-up + ingin enable fitur:

```
1. Tenant onboard
   - select plan, fill company profile
   - dashboard kosong (semua integrasi belum di-set)

2. Karyawan-related fitur jalan tanpa integrasi (employee CRUD, attendance, payroll calc)

3. Untuk fitur tertentu, owner buka Settings > Integrations
   - "Tambah Bank" → pilih format → isi config → test
   - "Tambah WhatsApp" → pilih format → isi config → test
   - "Tambah LLM" → pilih format → isi config → test (button "Fetch models")
   - dst.

4. Owner ke modul yang butuh integrasi (e.g., Settings > Payroll)
   - "Bank Disbursement Provider: ▼ [pilih dari bank yang sudah ditambah]"
   - "WhatsApp untuk slip gaji: ▼ [pilih]"

5. Modul jalan dengan provider yang owner pilih
```

**Preset templates (autofill convenience):**
- Disimpan di `storage/app/integration-presets/{domain}/*.json`
- Contoh `storage/app/integration-presets/bank/bca.json`:
  ```json
  {
    "name": "BCA KlikBCA Bisnis",
    "format": "batch_file_bca",
    "base_url": "",
    "hints": {
      "file_format_version": "v3",
      "account_required_fields": ["company_account_no", "branch_code"]
    },
    "documentation_url": "https://klikbca.com/help/payroll-format"
  }
  ```
- Admin UI baca file ini untuk **autofill suggestion** saat owner pilih format
- Code **tidak pernah** reference file ini saat runtime — pure autofill

**Owner bisa skip preset → input from scratch → semua tetap jalan.**

---

## 4. Logging & Observability

Setiap call ke external adapter di-log:

```sql
CREATE TABLE integration_logs (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT,
    adapter_config_id BIGINT,
    domain VARCHAR(50),
    method VARCHAR(50),
    direction VARCHAR(20),  -- 'outbound', 'inbound'
    request_payload JSON,    -- sanitized (no API key)
    response_status_code INT,
    response_payload JSON,
    duration_ms INT,
    error_message TEXT,
    cost DECIMAL(15,4),      -- jika applicable (LLM tokens, SMS, dll.)
    created_at TIMESTAMP
);
```

Owner dashboard ke `Settings > Integrations > Usage`:
- Per-adapter call count + success rate + avg latency
- Cost tracking (LLM tokens, SMS sent, OCR pages)
- Error rate alert

---

## 5. Security

### Encryption at Rest
- Kolom `api_key_encrypted` + `api_secret_encrypted` pakai Laravel Crypt facade (AES-256-GCM)
- Key rotation: tiap 90 hari, owner notif + re-encrypt
- Never returned in API response (mask format: `••••••••...XYZ` last 4 chars)

### Outbound Network Control
- Egress proxy filter: hanya allowlisted IP ranges (per adapter)
- Per-tenant outbound rate limit
- Block private IPs untuk webhook (SSRF prevention)

### Inbound Webhook
- HMAC-SHA256 signature verification (secret per webhook)
- Replay protection: timestamp + nonce, reject jika >5 min old
- Per-source rate limit

### Audit
- Setiap integration call masuk `integration_logs`
- Sensitive payload field di-redact (e.g., `pin`, `password`, `card_number`)
- Owner bisa lihat history, tidak bisa modify

---

## 6. Cost Tracking & BYOK Economics

### Owner Bawa Kunci Sendiri
- LLM cost: owner bayar provider langsung (OpenAI, DeepSeek, dll.)
- SMS cost: owner bayar gateway sendiri
- Bank disburse: biaya admin transfer ditanggung owner sesuai bank-nya
- Storage: owner pilih provider sesuai budget

**Kita charge subscription HRIS = pure feature value. Tidak markup integrasi.**

### Pricing per Adapter (optional, owner input)
```
[LLM Adapter "OpenAI Production"]
- Input cost: Rp 25 / 1k tokens
- Output cost: Rp 75 / 1k tokens

[SMS Adapter "Zenziva"]
- Cost per SMS: Rp 350
```

Dashboard:
- "Bulan ini Anda spend Rp 125.000 di LLM untuk CV screening 200 lamaran"
- "Bulan lalu Rp 380.000 untuk SMS OTP login"

Helps owner monitor + adjust provider.

---

## 7. Failure Handling

### Retry Strategy per Domain

| Domain | Retry | Strategy |
|---|---|---|
| Bank disburse | 3x | Exponential 5min, 30min, 2hr |
| E-sign | 5x | Exponential 1m, 5m, 30m, 2h, 12h |
| WhatsApp | 3x | Linear 30s, 60s, 120s |
| LLM | 2x | Immediate, then 5s |
| OCR | 2x | Immediate, then 10s |
| Mail | 5x | Exponential 1m, 5m, 30m, 2h, 12h |
| SMS | 3x | Linear 30s, 60s, 120s |
| Push | 2x | Immediate, then 5s |
| Storage | 3x | Exponential 1s, 5s, 30s |

### Dead Letter Queue
- Setelah max retry: masuk DLQ
- Alert ke owner + HR (untuk critical: bank, e-sign)
- Manual retry dari Filament UI

### Circuit Breaker
- Per-adapter circuit breaker
- Open state setelah 10 failures dalam 1 menit → 5 menit cooldown
- Closed kembali setelah 1 success dalam half-open

### Graceful Degradation
- WA fail → fallback ke email + push
- E-sign fail → fallback ke print + scan (manual)
- LLM fail → fitur disabled, no broken UX (jangan auto-fallback ke provider lain karena owner mungkin sudah specific)

---

## 8. Testing Strategy

### Mock Adapters
- `MockBankAdapter`, `MockLLMAdapter`, dll.
- Konfigurasi di `phpunit.xml` dengan `ADAPTER_*_MOCK=true`
- Useful untuk CI/CD tanpa hit external

### Contract Tests
- Per adapter format: test conformance to interface
- e.g., `tests/Feature/Adapters/Bank/BatchFileBCAFormatTest.php`

### End-to-End (Staging)
- Test account per vendor (sandbox)
- Run payroll dummy → bank batch file generated → upload ke iBank sandbox → verify
- Auto-test mingguan via cron

### Owner Test Mode
- Per adapter config: flag `is_test_mode`
- Owner test koneksi tanpa real transaction
- HRIS UI clear indicator "TEST MODE" saat aktif

---

## 9. Migration Path (Year 2+)

### Marketplace Add-ons
Untuk fitur yang **kita** value-add (bukan owner BYOK):
- Background check (kerjasama dengan vendor → kita resell)
- Bank account validation (cost ringan + frequent use → bundle dalam subscription)
- E-sign quota (jika owner tidak punya provider sendiri → kita resell)

Tetap dengan adapter dinamis di belakang, tapi owner punya opsi:
- BYOK (bayar provider langsung), atau
- "Marketplace" (kita resell, charge per use)

---

## 10. Summary

| Aspect | Approach |
|---|---|
| **Provider name** | NEVER hardcoded — owner input bebas |
| **Adapter class name** | Format-based (`BatchFileBCAAdapter`, NOT `BCAAdapter`) |
| **DB schema** | Generic `adapter_configs` per domain |
| **Selection** | Per-feature dropdown dari user-added providers |
| **Preset** | Autofill only, runtime tidak baca preset file |
| **Encryption** | API key encrypted at rest, masked di output |
| **Cost** | Owner input pricing untuk tracking; kita tidak maintain |
| **Onboarding** | Tenant fresh = kosong; owner add as needed |
| **Fallback** | NO auto-fallback ke vendor lain (owner choice respected) |
| **Marketing message** | "BYOK + switch anytime — no vendor lock" |

---

## 11. Open Questions

- Q1: Boleh kita sediakan "default Marketplace provider" untuk fitur yang sangat technical (e.g., e-sign untuk owner yang belum paham PSrE)? **Default decision:** Year 2 launch marketplace, Phase 1-6 BYOK only.
- Q2: Format-based adapter sufficient atau perlu vendor-specific kasus tertentu? **Default decision:** Format-based default; vendor-specific hanya jika ada feature unique yang tidak bisa di-generic (e.g., DJP Coretax sangat spesifik).
- Q3: Multi-tenant key isolation: tenant A key tidak bisa di-access by tenant B? **Default decision:** Yes, encrypted with tenant-scoped key + global master key (envelope encryption).
