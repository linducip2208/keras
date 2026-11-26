# 01 — Architecture Audit

## Arsitektur aktual

```
Flutter (ESS) ──REST /api/v1──▶ Laravel 13 ──Filament 4.11 (admin panel)
        │                           │  ├─ Services: Payroll, Tax, BPJS, Overtime,
        │                           │  │  Approval, Payslip, Billing, AI, WhatsApp
        │                           │  └─ Adapters: Payment, LLM, WA, E-Sign(dummy)
        │                           ├─ Middleware: RequirePair, IdentifyTenant,
        │                           │  ResolveApiTenant (baru, post-auth)
        │                           └─ Traits: BelongsToTenant (server-enforced)
        └─ SecureStorage(token)      DB MySQL/SQLite, queue database (sync di test)
```

## Keputusan arsitektur

1. **Tenant resolution dua lapis**: `IdentifyTenant` (pre-auth: subdomain/custom
   domain, tanpa fallback acme) + `ResolveApiTenant` (post-auth: dari
   `user->tenant_id`, fail-closed 403). Header `X-Tenant-ID` tidak lagi dipercaya.
2. **Tenant enforcement di model**: `BelongsToTenant::creating` memaksa tenant milik
   user login non-super-admin — menutup mass-assignment `tenant_id` di 40 model
   tanpa mengubah fillable (kompatibel Filament super-admin).
3. **API contract stabil**: 16 endpoint; tambahan `POST /attendance/photo`.
   Tidak ada breaking change pada kontrak lama.
4. **Regulasi per-tahun**: `PPh21TerCalculator::setYear()`,
   `BPJSCalculator::setYear()` — tahun mengikuti tanggal payroll, bukan `now()`.

## Bukan arsitektur yang diubah

- Tetap monolit Laravel + Filament + Flutter (tidak ada microservice).
- Tetap Sanctum token (tanpa refresh rotation — dicatat di technical debt).
- Scheduler tetap kosong (tidak ada logika fake) — lihat technical debt.
