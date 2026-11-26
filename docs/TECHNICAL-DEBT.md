# Technical Debt

Lihat `docs/audit/08-TECHNICAL-DEBT.md` (18 item P1–P3). Ringkasan eksekutif:

- **P1 correctness**: unique per-tenant (T-2), enkripsi rekening disbursement,
  rekonsiliasi progresif Desember, validasi usia JP, enkripsi PDF (qpdf).
- **P2 hardening**: login throttle, webhook signature, Spatie teams, geofence,
  scheduler nyata (eskalasi/akrual/pengingat/retry), FK & index minor.
- **P3 fitur**: token rotation, FCM/deep-link/offline, PDF parser CV,
  DJP/SIPP submit, 5 modul PLANNED, expression parser (ganti `eval`),
  `attendance_based` sinkron hadir.

Aturan: item debt hanya boleh dicoret dengan bukti test, bukan klaim.
