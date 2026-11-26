# 04 — API Audit

## Kontrak v1 (stabil, tanpa breaking change)

| Method | Path | Keterangan |
|---|---|---|
| POST | `/v1/auth/login` | +hapus token duplikat per device_name |
| POST | `/v1/auth/logout` | — |
| GET | `/v1/me` | — |
| POST | `/v1/attendance/photo` | **BARU** — upload multipart, return server path |
| POST | `/v1/attendance/check-in` | +tolak mock-GPS, +shift dari jadwal, +validasi foto server |
| POST | `/v1/attendance/check-out` | +tolak mock-GPS, +terima is_mock_gps, +validasi foto server |
| GET | `/v1/attendance/today` | — |
| GET | `/v1/attendance/logs` | dipakai mobile (riwayat) |
| GET/POST | `/v1/leave/*` | +tenant-scope type, +atomic lock |
| GET | `/v1/payslips`, `/v1/payslips/{id}` | dipakai mobile (detail breakdown) |
| GET/POST | `/v1/approvals/*` | guard engine idempoten |

## Auth & isolasi

- Semua endpoint privat di dalam `['auth:sanctum','tenant.api']`.
- `ResolveApiTenant` fail-closed 403 untuk user tanpa tenant / tenant suspended.
- Rate limit login: BELUM — dicatat debt (throttle).

## Konsumen

- Flutter: semua endpoint MATCH; 3 gap MISSING sudah dipakai
  (`/logs`, `/{id}`, `POST leave`) kecuali overtime/reimburse (backend belum ada).
- WA webhook: Meta/Fonnte/Wati normalisasi; tanpa verifikasi signature (debt).
