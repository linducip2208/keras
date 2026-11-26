# Payroll Test Scenarios

| # | Skenario | Test | Status |
|---|---|---|---|
| 1 | Lembur 1/3/8/10 jam workday & libur 5/6-day | OvertimeCalculatorTest | ✅ PASS |
| 2 | TER dikenakan ke bruto (tanpa biaya jabatan/BPJS) | PPh21TerCalculatorTest | ✅ PASS |
| 3 | Kategori A/B/C mapping | PPh21TerCalculatorTest | ✅ PASS |
| 4 | Cap Kes 12jt, JP 10.042.300 | BPJSCalculatorTest | ✅ PASS |
| 5 | Konsistensi total = jumlah breakdown | BPJSCalculatorTest | ✅ PASS |
| 6 | Cross-tenant leave IDOR 404 | TenantIsolationTest | ✅ PASS |
| 7 | User tanpa tenant 403 | TenantIsolationTest | ✅ PASS |
| 8 | End-to-end run payroll + lock + slip + disburse | — | ❌ BELUM (butuh seed penuh) |
| 9 | Karyawan join/resign mid-month prorata | — | ❌ BELUM |
| 10 | Rekonsiliasi progresif Desember | — | ❌ BELUM (debt P1) |
