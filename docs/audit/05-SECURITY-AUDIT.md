# 05 — Security Audit

## Diperbaiki (dengan bukti test/kode)

| ID | Temuan | Perbaikan |
|---|---|---|
| CRITICAL-1 | TenantScope mati di API | `tenant.api` middleware post-auth di `routes/api.php` + test `user_without_tenant → 403` |
| CRITICAL-2 | X-Tenant-ID dipercaya + fallback acme | Dihapus; auth user menang; tanpa fallback |
| CRITICAL-3 | Fail-open saat DB error | Fail-closed 403 di `ResolveApiTenant`; publik tetap jalan |
| CRITICAL-4 | Login global ambigu | Unique email sudah ada (bawaan Laravel) — diverifikasi, bukan diasumsikan |
| HIGH-1 | tenant_id mass-assignable | `BelongsToTenant::creating` paksa tenant user non-super-admin |
| HIGH-2 | Adapter lintas tenant + token lemah | Scope `where tenant + is_active`; success/cancel URL via token invoice |
| HIGH-3 | Webhook tanpa secret + log PII | Adapter nonaktif ditolak 404; header sensitif di-strip dari log |
| IDOR cuti | `leave_type_id` lintas tenant | `where tenant → findOrFail` + test 404 |
| IDOR WA | `LeaveType::find` + step tanpa tenant | Tenant-scoped + guard |
| Klaim menyesatkan | Password PDF, selfie path, password WA | Dihapus/dijujurkan di kode + UI |

## Tersisa (diterima/ditunda jujur)

1. **Login throttling** — belum ada `throttle` di `/auth/login`. Debt P2.
2. **Verifikasi signature webhook** (payment + WA) per-vendor. Debt P2.
3. **Spatie teams** untuk roles per-tenant. Mitigasi parsial aktif. Debt P2.
4. **T-2 unique per-tenant** (assets/invoices/payments). Butuh audit data existing. Debt P1.
5. **Enkripsi `disbursement_items.account_number`** (keputusan PDP). Debt P1.
6. **Geofence radius** attendance (jarak ke branch belum dihitung). Debt P2.
7. **Sanctum token rotation/refresh** + biometric gate. Debt P3.
8. **FCM push + deep-link approval**. Belum ada. Debt P3.

Tidak ada credential di source code ( diverifikasi via grep adapters: semua dari DB/env).
