# Roadmap Next

## Sprint 1 — Production gate (P1)

- Unique per-tenant (T-2) + audit data existing.
- Enkripsi/masking rekening disbursement (keputusan PDP).
- Rekonsiliasi PPh progresif Desember + test.
- Validasi usia JP + rentang JKK.
- qpdf/Signed-URL untuk slip + hapus file lama.
- Login throttle + webhook signature dasar.
- Scheduler: eskalasi approval + akrual cuti + trial expiry.
- Backup + monitoring + runbook deploy (`docs/deployment/`).

## Sprint 2 — Modul bisnis

- E2E payroll test + lock/approve flow UI lengkap + accounting export.
- Geofence radius + overtime request mobile + reimbursement + receipt upload.
- Onboarding checklist + Travel/SPPD + LMS dasar + Kompetensi dasar.
- Report builder + export Excel/PDF + scheduled email.
- Spatie teams + Tenant/User management untuk super-admin.
- E-sign adapter nyata pertama (Privy/BSrE) untuk kontrak & paklaring.

## Sprint 3 — Scale

- FCM + deep-link + offline queue + biometric.
- Direct DJP Coretax & SIPP (saat API live) + klaim JKK/JKM workflow.
- AI narrative insight terekspos ke dashboard + turnover risk.
- pSEO + landing + pricing + billing self-serve penuh.
