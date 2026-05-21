# 03 — System Architecture

## 1. High-Level Architecture

```
                     ┌──────────────────────────────────────┐
                     │     EDGE / CDN  (Cloudflare)        │
                     │  • Cache static assets               │
                     │  • DDoS protection                   │
                     │  • SSL termination                   │
                     └────────────┬─────────────────────────┘
                                  │
              ┌───────────────────┼───────────────────┐
              │                   │                   │
         Web Public          Web Admin            Mobile App
         (Public-SEO)        (Filament 4)        (Flutter)
              │                   │                   │
              │                   │                   │
              │           ┌───────┴───────┐           │
              │           │               │           │
              │           ▼               ▼           │
              │      ┌──────────────────────────┐    │
              └────►│  Laravel 13 Monolith     │◄───┘
                    │  • Auth (Sanctum)        │
                    │  • Multi-tenant          │
                    │  • License v3 gate       │
                    │  • API REST + GraphQL    │
                    │  • Webhook engine        │
                    └────┬────────────┬────────┘
                         │            │
                ┌────────┴───┐    ┌──┴──────────────────┐
                │            │    │                     │
                ▼            ▼    ▼                     ▼
           ┌─────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐
           │ MySQL 8 │  │  Redis   │  │ S3-compat│  │  Queue   │
           │ Primary │  │ Cache+   │  │ Storage  │  │ Horizon  │
           │ + Read  │  │ Session  │  │  Files   │  │ workers  │
           │ Replica │  │ Queue    │  └──────────┘  └────┬─────┘
           └─────────┘  └──────────┘                     │
                                                          ▼
                                    ┌────────────────────────────────┐
                                    │  External Adapters (Dynamic)   │
                                    │  • Bank disburse (Flip/Xendit) │
                                    │  • E-sign (BSrE/Privy)         │
                                    │  • WA Business API             │
                                    │  • LLM AI (BYOK)               │
                                    │  • OCR receipt                 │
                                    │  • Mail provider               │
                                    │  • SMS gateway                 │
                                    │  • DJP Coretax API             │
                                    │  • BPJS SIPP API               │
                                    └────────────────────────────────┘
```

## 2. Tech Stack

### Backend
| Layer | Choice | Versi | Alasan |
|---|---|---|---|
| Language | PHP | 8.3+ | Stack-lock |
| Framework | Laravel | 13.x | Stack-lock |
| Admin Panel | Filament | 4.x | Stack-lock + cepat develop |
| Auth API | Laravel Sanctum | 4.x | Token-based for mobile |
| Auth Admin | Filament built-in | — | 2FA support native |
| Queue | Laravel Horizon + Redis | latest | Monitoring + retry |
| Cache | Redis | 7+ | Cache + session + queue |
| Search | MySQL FULLTEXT (basic) → Meilisearch (later) | — | Start simple |
| Permission | Spatie Laravel Permission | 7+ | Battle-tested |
| Audit | Spatie Activitylog | 4+ | Battle-tested |
| Multi-tenant | Stancl Tenancy v4 | latest | Database per tenant OR row-level |

### Database
| Item | Choice | Alasan |
|---|---|---|
| Primary DB | MySQL 8.0 | Stack-lock; transaction kuat |
| Read Replica | MySQL slave | Read scale |
| Schema strategy | **Single DB, row-level tenant_id** (default), opsi DB-per-tenant untuk enterprise | Cost-efficient untuk SMB |
| Migration tool | Laravel migration | Native |
| ORM | Eloquent | Native Laravel |

### Frontend
| Layer | Choice | Versi |
|---|---|---|
| Admin UI | Filament 4 (Livewire 3 + Alpine + Tailwind) | latest |
| Public Web (pSEO) | Blade + Tailwind + Alpine | native Laravel |
| Mobile | Flutter | 3.24+ |
| State (mobile) | Riverpod | latest |
| HTTP (mobile) | Dio | latest |
| Storage (mobile) | Hive (offline cache) + secure storage | latest |

### Infrastructure
| Item | Choice | Alasan |
|---|---|---|
| Hosting | VPS Indonesia (Biznet/Niagahoster) → AWS Singapore on growth | UU PDP data residency |
| Web Server | Nginx + PHP-FPM (Octane optional) | Standard |
| Container | Docker Compose (dev) → K8s (scale) | Standard |
| CDN | Cloudflare | Free tier cukup awal |
| Object Storage | MinIO (self-host) atau Wasabi atau S3 | S3 API compatibility |
| Monitor | Sentry (errors) + Uptime Kuma + Grafana | OSS-friendly |
| Logs | Laravel log → Logstash → OpenSearch (later) | Centralized |
| CI/CD | GitHub Actions atau Gitea Actions | Free tier |

### Integration Adapters (per CLAUDE.md §2 — semua dinamis)
| Domain | Format-based Adapters (generic) |
|---|---|
| **Bank disburse** | `RedirectBasedAdapter` (Flip), `RestApiAdapter` (Xendit, BCA Auto Collect), `BatchFileAdapter` (KlikBCA Bisnis, Mandiri Cash Mgmt, BNI Direct, BRI CMS, CIMB BizChannel) |
| **E-signature** | `BSrEFormatAdapter`, `PrivyFormatAdapter`, `GenericPKIAdapter` |
| **WA Business** | `OfficialMetaAdapter`, `Cloud360Adapter`, `WatiAdapter`, `FonneteAdapter`, `MaytapiAdapter` (semua REST format) |
| **LLM AI** | `OpenAICompatibleAdapter` (covers OpenAI, DeepSeek, Groq, OpenRouter, Together, Ollama, LM Studio, vLLM, Anyscale, Cerebras, dst.), `AnthropicFormatAdapter`, `GeminiFormatAdapter` |
| **OCR** | `OpenAIVisionAdapter`, `GeminiVisionAdapter`, `TextractAdapter`, `MindeeAdapter` |
| **Mail** | `SMTPAdapter`, `MailgunRestAdapter`, `SendGridRestAdapter`, `SESRestAdapter`, `PostmarkRestAdapter`, `BrevoRestAdapter` |
| **SMS** | `RestApiAdapter` (Twilio, Vonage, Wavecell, Zenziva, JatisMobile), `SMPPAdapter` |
| **DJP** | `CoretaxApiAdapter` (saat live 2026) |
| **BPJS** | `SIPPApiAdapter` (saat tersedia), default fallback `SIPPFileExportAdapter` |
| **Accounting** | `AccurateApiAdapter`, `JurnalApiAdapter`, `XeroApiAdapter`, `OdooApiAdapter`, generic `WebhookOutboundAdapter` |
| **Storage** | `S3CompatibleAdapter` (MinIO, Wasabi, R2, B2, AWS S3, GCS via S3 API) |
| **Push Notif** | `FCMAdapter` (sudah generic) |

## 3. Layered Architecture (Backend)

```
┌─────────────────────────────────────────────────────────────┐
│  Presentation Layer                                          │
│  • Filament Resources (admin)                                │
│  • API Controllers (mobile + integrations)                   │
│  • Blade Views (pSEO + public)                               │
│  • Livewire Components (admin micro-interactions)            │
└────────────────────────┬────────────────────────────────────┘
                         │
┌────────────────────────┴────────────────────────────────────┐
│  Application Layer (Services / Actions / Use Cases)         │
│  • PayrollRunService                                         │
│  • AttendanceCheckInAction                                   │
│  • LeaveRequestService                                       │
│  • TaxCalculationService (PPh 21 TER)                        │
│  • BPJSCalculationService                                    │
│  • ApprovalWorkflowEngine                                    │
│  • NotificationDispatcher                                    │
│  • LicenseGateService                                        │
└────────────────────────┬────────────────────────────────────┘
                         │
┌────────────────────────┴────────────────────────────────────┐
│  Domain Layer (Models + Business Rules)                      │
│  • Employee, Contract, Payroll, Attendance, Leave, etc.      │
│  • Value Objects (Money, NIK, NPWP, PTKP, BankAccount)       │
│  • Domain Events (PayrollGenerated, EmployeeJoined, etc.)    │
│  • Policy (authorization rules)                              │
└────────────────────────┬────────────────────────────────────┘
                         │
┌────────────────────────┴────────────────────────────────────┐
│  Infrastructure Layer                                        │
│  • Eloquent Repositories                                     │
│  • External Adapters (Bank, E-sign, WA, LLM, OCR, etc.)     │
│  • Queue Workers (Horizon)                                   │
│  • Cache Repository (Redis)                                  │
│  • File Storage (S3-compatible)                              │
│  • Audit Log writer                                          │
└─────────────────────────────────────────────────────────────┘
```

### Folder Structure (Laravel)
```
app/
├── Console/Commands/
├── Filament/                       # Admin panels
│   ├── Resources/                  # 35+ resources
│   ├── Pages/                      # custom pages
│   └── Widgets/                    # dashboard
├── Http/
│   ├── Controllers/
│   │   ├── Api/                    # mobile + integration API
│   │   ├── Web/                    # public pSEO pages
│   │   └── Webhook/                # inbound webhook
│   ├── Middleware/
│   │   ├── TenantScope.php
│   │   ├── LicenseGate.php
│   │   └── AuditLog.php
│   └── Requests/                   # form validation
├── Models/                         # 80+ eloquent models
├── Services/                       # business logic
│   ├── Payroll/
│   ├── Attendance/
│   ├── Tax/
│   ├── BPJS/
│   ├── Leave/
│   ├── Approval/
│   └── Recruitment/
├── Adapters/                       # external integrations
│   ├── Bank/
│   ├── ESign/
│   ├── WhatsApp/
│   ├── LLM/
│   ├── OCR/
│   ├── Mail/
│   ├── SMS/
│   └── Accounting/
├── Jobs/                           # queue jobs
├── Events/
├── Listeners/
├── Policies/
├── Enums/
├── ValueObjects/
└── Support/
    ├── License/                    # license v3 client
    └── Tenant/

config/
├── hris.php                        # main config
├── tenancy.php
└── adapters.php                    # adapter registry

database/
├── migrations/                     # ~100+ migrations
├── seeders/
│   ├── HolidaySeeder.php
│   ├── PTKPSeeder.php
│   ├── BPJSRateSeeder.php
│   ├── DemoCompanySeeder.php
│   └── DemoEmployeeSeeder.php
└── factories/

resources/
├── views/
│   ├── pseo/                       # programmatic SEO pages
│   ├── public/                     # landing
│   └── filament/                   # admin customizations
└── lang/
    ├── id/
    └── en/

routes/
├── web.php                         # public + pSEO
├── api.php                         # mobile + integration
├── webhook.php
└── tenant.php

tests/
├── Unit/
├── Feature/
└── Browser/                        # Dusk untuk e2e
```

## 4. Multi-Tenancy Strategy

### Approach: **Hybrid Single-DB Row-level + Optional DB-per-tenant**

**Default (Single-DB row-level):**
- Semua tabel punya `tenant_id` foreign key
- Global scope di Eloquent model auto-filter
- Middleware `TenantScope` set context dari subdomain / API token
- Cost-efficient untuk Starter–Essential tier

**Enterprise / Suite (DB-per-tenant):**
- Database terpisah per tenant
- Routing via `stancl/tenancy` package
- Higher security isolation
- Backup independent per tenant

### Tenant Identification
- **Subdomain:** `acme.hris.app` → tenant `acme`
- **Custom domain:** `karyawan.acme.co.id` → tenant `acme` (DNS CNAME)
- **API header:** `X-Tenant-ID: acme` untuk integration
- **License v3 token:** embed `tenant_id` claim

### Cross-tenant Concerns
- **Master data** (e.g., holiday Indonesia, PTKP, BPJS rate) → global table (no tenant_id), read-only oleh tenant
- **System log** → central tabel admin-only
- **Payment / billing** → central tabel admin-only

## 5. Authentication & Authorization

### Authentication
| User Type | Method |
|---|---|
| Admin (HRD, owner) | Filament login + 2FA (TOTP) wajib |
| Karyawan (web) | Email/NIK + password atau magic link |
| Karyawan (mobile) | Sanctum personal access token (long-lived) |
| Karyawan (WA bot) | Phone OTP first time + persistent session |
| Integration / Webhook | API key per integration (rotatable) |
| Super-admin (internal) | Separate `central` guard |

### Authorization
- **Roles:** Super Admin, Tenant Owner, HR Admin, HR Staff, Finance, Manager, Employee, Auditor, Read-only
- **Permissions:** granular per resource × action (e.g., `payroll.run`, `payroll.approve`, `payroll.view-own`)
- **Spatie Permission** package
- **Policy** classes per Eloquent model
- **Row-level:** Manager hanya lihat dept-nya; Employee lihat data sendiri

### Sample Permission Matrix
| Permission | Owner | HR Admin | HR Staff | Finance | Manager | Employee |
|---|---|---|---|---|---|---|
| `employee.view-all` | ✅ | ✅ | ✅ | ✅ | dept-only | self-only |
| `employee.edit` | ✅ | ✅ | limited | ❌ | ❌ | ❌ |
| `payroll.run` | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| `payroll.approve` | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ |
| `payroll.view-own-slip` | — | — | — | — | — | ✅ |
| `leave.approve` | ✅ | ✅ | ❌ | ❌ | dept-only | ❌ |
| `reimburse.approve` | ✅ | ✅ | ❌ | ✅ | dept-only | ❌ |
| `report.view-all` | ✅ | ✅ | ❌ | ✅ | dept-only | ❌ |

## 6. API Design

### REST API
- **Base URL:** `/api/v1/`
- **Auth:** Bearer Sanctum token
- **Tenant context:** subdomain atau `X-Tenant-ID` header
- **Format:** JSON only
- **Versioning:** URI path `/v1/`, `/v2/`
- **Pagination:** cursor-based (default 20, max 100)
- **Filter:** Spatie Query Builder
- **Idempotency:** `Idempotency-Key` header untuk write operations
- **Rate limit:** 100 req/min default, configurable

### Resource Naming
```
GET    /api/v1/employees                  # list
POST   /api/v1/employees                  # create
GET    /api/v1/employees/{id}             # detail
PATCH  /api/v1/employees/{id}             # update
DELETE /api/v1/employees/{id}             # soft delete

POST   /api/v1/attendance/check-in        # special action
POST   /api/v1/attendance/check-out
POST   /api/v1/leave/{id}/approve         # state transition
POST   /api/v1/payroll/runs               # trigger payroll
GET    /api/v1/payroll/runs/{id}/status   # polling status
```

### Webhook (outbound)
- Tenant register webhook URL per event
- Events: `employee.joined`, `employee.resigned`, `payroll.completed`, `leave.approved`, dst.
- Retry: 5x exponential backoff (1m, 5m, 30m, 2h, 12h)
- Signed via HMAC-SHA256 dengan secret

## 7. Mobile App Architecture (Flutter)

### Layers
```
lib/
├── core/
│   ├── network/        # Dio client, interceptor
│   ├── storage/        # Hive + secure storage
│   ├── auth/
│   └── tenant/
├── features/
│   ├── attendance/
│   │   ├── data/
│   │   ├── domain/
│   │   └── presentation/
│   ├── leave/
│   ├── payslip/
│   ├── reimbursement/
│   ├── approval/
│   └── profile/
├── shared/
│   ├── widgets/
│   ├── theme/
│   └── utils/
└── main.dart
```

### Key Capabilities
- Offline-first via Hive cache
- GPS via `geolocator` + mock detection
- Camera + face detection via `google_mlkit_face_detection`
- Push via FCM
- Biometric login (fingerprint/face) via `local_auth`
- File picker + image compression
- Background sync (attendance queue saat offline)

## 8. Security Architecture

### Defense-in-Depth Layers

**Edge (Cloudflare)**
- DDoS mitigation
- Bot protection
- WAF rules untuk OWASP Top 10
- Rate limiting per IP

**Application**
- CSRF token (web)
- XSS protection (Blade escaping)
- SQL injection: Eloquent + parameterized
- Input validation via Form Request
- Output encoding
- CSP header
- HSTS strict
- HTTPS-only cookie + SameSite

**Auth & Access**
- 2FA TOTP wajib admin
- Password: Argon2id, min 12 char, lower+upper+digit+symbol
- Account lockout 10 attempt, exponential backoff
- Session timeout configurable
- Concurrent session limit
- IP whitelist opsional per tenant
- Audit log semua login + logout + failed attempt

**Data**
- Encryption at rest (AES-256-GCM) untuk:
  - PII (NIK, NPWP, alamat, phone)
  - Salary & payroll
  - Bank account
  - API keys integration
- Encryption in transit: TLS 1.3 only
- Field-level encryption via Laravel encrypted cast
- Secret rotation policy (90 hari untuk API keys)

**Integration**
- Sandboxed adapter (timeout, isolated process)
- Outbound traffic via egress proxy + allowlist
- Webhook signature HMAC-SHA256
- API key hashed di storage (bcrypt)

**Audit & Compliance**
- Activity log immutable (Spatie + append-only S3 archive)
- PII access log (siapa lihat data karyawan siapa kapan)
- Retention 7 tahun (UU PDP)
- Right-to-be-forgotten workflow (data deletion request)
- Right-to-export (data portability JSON dump)
- Breach notification process

## 9. Deployment Strategy

### Environments
| Env | Purpose | URL |
|---|---|---|
| Local | Developer | `hris.test` (Laravel Sail) |
| Staging | QA + UAT | `staging.hris.app` |
| Production SaaS | Production multi-tenant | `*.hris.app` + custom domains |
| Demo | Sales demo + sandbox | `demo.hris.app` |
| Self-host (License) | Customer-hosted | customer's infra |

### Deployment Pipeline
```
Developer push → GitHub
                  ↓
          GitHub Actions
          • PHPStan + Pint
          • PHPUnit (unit + feature)
          • Flutter test
          • Build assets
                  ↓
          Auto-deploy staging
                  ↓
          Manual approval
                  ↓
          Deploy production (blue-green)
          • DB migration safe-mode
          • Cache flush selective
          • Queue restart graceful
          • Health check
                  ↓
          Smoke test + rollback hook
```

### Rollback Strategy
- DB migration: always reversible (atau ada `down()` + backup snapshot)
- Code: blue-green; instant rollback to previous container
- Asset: versioned (CDN cache busting)
- Feature flag: kritis fitur behind toggle (Laravel Pennant)

### Backup & DR
- DB: harian incremental + mingguan full, retention 90 hari, encrypted di S3 region berbeda
- File storage: cross-region replication
- Config (env, secrets): Vault atau HashiCorp Vault OSS
- Drill recovery: 6-bulanan, target RTO 4 jam, RPO 15 menit

## 10. Observability

### Metrics
- App: request rate, latency P50/P95/P99, error rate
- DB: query time, slow query log, connection pool
- Queue: job rate, fail rate, dead-letter count, retry rate
- Business: WAPC (weekly active payroll companies), payroll runs/day, MAU

### Logs
- Structured JSON
- Correlation ID per request (propagate ke job + integration)
- Severity: debug, info, warn, error, critical
- PII redaction otomatis

### Tracing
- OpenTelemetry-ready (Phase 5+)
- Spans untuk: HTTP request, DB query, queue job, external adapter call

### Alerting
- PagerDuty / Slack webhook
- Trigger: error rate >1%, latency P95 >5s, payroll job stuck >30m, queue lag >10k

## 11. Data Residency & Compliance

- **UU PDP:** tenant Indonesia → server Indonesia. Konfigurable per tenant.
- **Tenant pilih region** saat onboard
- **Cross-border data transfer** hanya dengan consent + DPA
- **Right to access:** API endpoint untuk karyawan request data sendiri
- **Right to deletion:** workflow approval (soft delete 30 hari → hard delete)
- **Right to portability:** export JSON+CSV semua data karyawan
- **Breach notification:** automated email ke DPO + regulator dalam 72 jam

## 12. License v3 Integration

**Sumber:** kit drop-in dari `whitelabel.co.id`

**Komponen:**
- `app/Services/LicenseClient.php` — verifier
- `app/Http/Controllers/PairController.php` — wizard `/__pair`
- `storage/license/license.lock` — AES-256-GCM encrypted
- RSA public key embedded di code
- Heartbeat ping ke license server (configurable)

**Modes:**
- **Trial:** 14 hari, watermark di UI
- **SaaS:** verified per-request via license server
- **License-once self-host:** verified saat boot, offline-tolerant 30 hari
- **Whitelabel reseller:** sub-license token

**Gate placement:**
- Middleware `LicenseGate` di group route
- Filament panel boot check
- Background job boot check
- API request middleware

---

## 13. Why This Stack (Trade-offs Summary)

| Choice | Pro | Con | Mitigation |
|---|---|---|---|
| Laravel monolith | Ship fast, dev velocity | Scale ceiling | Horizontal scale + queue + cache |
| MySQL row-level tenant | Cost-efficient | Risk noisy neighbor | Per-tenant DB option enterprise |
| Filament admin | Cepat develop | Bound to Livewire | Don't fight framework, customize via widget |
| Flutter mobile | Single codebase | Bukan native polish | OK untuk MVP, evaluate ulang Year 2 |
| BYOK dynamic adapters | No vendor lock, fleksibel | More code complexity | Strong adapter contract + test matrix |
| License v3 | Sales channel + protection | Maintenance overhead | Reuse dari project lain (battle-tested) |
| Self-host option | Capture enterprise market | Support burden | Tier license dengan limited support |
