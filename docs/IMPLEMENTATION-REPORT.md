# Implementation Report

## Yang dikerjakan (dengan bukti)

### A. Keamanan (Phase 2)

1. `app/Http/Middleware/IdentifyTenant.php` — hapus `X-Tenant-ID` & fallback acme.
2. `app/Http/Middleware/ResolveApiTenant.php` (baru) — binding post-auth, fail-closed.
3. `bootstrap/app.php` — api middleware + alias `tenant.api`.
4. `routes/api.php` — grup `['auth:sanctum','tenant.api']`.
5. `app/Models/Concerns/BelongsToTenant.php` — enforcement tenant server-side.
6. Billing: token URL, scope adapter, strip header log. Billing: PPN configurable.
7. WA BotEngine: tenant-scope leave type & step, pesan slip jujur.

### B. Payroll (Phase 2)

8. TER langsung ke bruto; `setYear()` di Tax & BPJS; periode dari payroll date;
   null-guard period; `gross_basis` bruto asli.
9. ApprovalEngine idempoten + float-cast bindings.
10. LeaveController atomic + tenant-scope + test.
11. PayslipPdfService: slipNumber periode + hapus klaim password.
12. CVScreening: docx/txt + NEEDS-PDF-PARSER + tenant guard adapter.

### C. Attendance (Phase 2–5)

13. Tolak mock-GPS (config), shift dari jadwal, endpoint upload foto + validasi.
14. Flutter multipart + riwayat + pesan ramah + checkout mock flag.

### D. Filament (Phase 3–6)

15. Approve/Reject actions, status filter, kolom requester/workflow.
16. KeyResults + Steps relation managers; relasi model baru (4).
17. Resource read-only Tax & BPJS (8 file).
18. PayrollWizard hardening (duplikat guard, error handling, company scope).

### E. Flutter (Phase 5)

19. Manifest permissions; login debug-only + device stabil + show/hide;
   onUnauthorized; form cuti; riwayat; detail slip; geolocator API baru;
   hapus unused field. Analyze bersih, test pass.

### F. QA (Phase 8)

20. 13 test baru (overtime 6, TER 3, BPJS 3, tenant 2 — 1 butuh Company+gender
    fix selama penulisan, tercatat transparan); ExampleTest selaras license gate.
21. `flutter pub get` + analyze + widget test baru.

## Yang TIDAK dikerjakan (masuk debt/roadmap)

Scheduler, onboarding/LMS/travel/kompetensi/e-sign UI, report builder, FCM,
offline queue, geofence, PDF parser, DJP/SIPP submit, T-2 unique, enkripsi
rekening disbursement, login throttle, webhook signature, Spatie teams.
