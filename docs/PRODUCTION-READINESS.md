# Production Readiness

## Status: BELUM SIAP PRODUCTION. Siap untuk: demo, UAT terbatas, pilot 1 tenant.

## Checklist

| Item | Status |
|---|---|
| Tenant isolation inti | ✅ (+test) |
| Payroll TER correctness | ✅ formula (+test), ⚠️ rekonsiliasi Desember belum |
| Approval idempoten | ✅ |
| Backup DB harian + PITR | ❌ belum ada prosedur |
| Queue worker + scheduler + monitoring | ❌ scheduler kosong; worker manual |
| Log rotation + error monitoring | ❌ belum (Sentry/dsb) |
| Health check `/up` | ✅ bawaan Laravel |
| Rate limiting login/API | ❌ |
| Validasi akuntan atas TER/BPJS/lembur | ❌ NEEDS VALIDATION |
| Enkripsi PII at-rest tambahan | ⚠️ NIK/NPWP/rekening master encrypted; disbursement plain |
| Flutter release build (keystore, icons, flavors) | ❌ debug only |
| Secrets management prod | ⚠️ env-based; pastikan tidak commit `.env` |

## Syarat naik ke production

1. Selesaikan debt P1 (5 item) + validasi akuntan (tanda tangan).
2. Aktifkan scheduler + worker + backup + monitoring + throttle.
3. E2E payroll test + UAT 1 tenant + security re-scan.
4. Flutter release pipeline (signing, FCM project, `google-services.json` via CI secret).
