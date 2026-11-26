# 06 — Flutter Audit

## Baseline awal (valid setelah `pub get`)

- 10 file lib, 6 layar inti MATCH dengan backend; struktur lebih tipis dari klaim README.
- Klaim palsu: selfie path lokal, mock detection tanpa enforcement, PDF viewer, offline queue, FCM, biometrik, org chart.

## Diperbaiki

| Gap | Perbaikan |
|---|---|
| Form cuti tidak ada | Bottom sheet penuh (types/dates/half-day/reason) + error/empty state |
| `leave_type` key salah | Baca `leaveType` + fallback + null-guard angka |
| Selfie path lokal | Upload multipart → server path; backend validasi keberadaan file |
| Mock hanya telemetri | Server tolak 422; mobile kirim flag di in & out |
| Pesan error mentah | `_friendlyError` (422 → pesan server; timeout → pesan jaringan) |
| History tidak dipakai | Riwayat 30 hari di Attendance |
| Detail slip TODO | Bottom sheet breakdown earning/deduction/PPh/BPJS/net |
| 401 menggantung | `onUnauthorized` → invalidate auth → kembali Login |
| Kredensial demo di repo | Prefill hanya `kDebugMode` |
| Manifest kosong | INTERNET, location, camera, biometric |
| Analyze ~100 error | **No issues found** |
| Test | `widget_test` AuthGate pass |

## Belum (jujur, tanpa klaim)

Offline queue, FCM, deep-link, biometrik login, overtime/reimburse (backend
belum ada), org chart, profile edit, shift schedule view, face anti-spoofing
(MLKit tidak ada — JANGAN dijanjikan).
