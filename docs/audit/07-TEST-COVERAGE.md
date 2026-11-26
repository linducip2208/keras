# 07 — Test Coverage

## Laravel (`php artisan test`): 16/16 PASS

| Suite | Test | Bukti |
|---|---|---|
| Unit | OvertimeCalculatorTest (6) | Kepmenaker: 1.5x/2x/3x/4x, /173 |
| Unit | PPh21TerCalculatorTest (3) | TER langsung ke bruto; kategori A/B/C |
| Unit | BPJSCalculatorTest (3) | Cap Kes 12jt, JP 10.042.300, total konsisten |
| Feature | TenantIsolationTest (2) | IDOR cuti 404; user tanpa tenant 403 |
| Feature | ExampleTest (1→1) | License gate redirect `/__pair` |
| Unit | ExampleTest (1) | Scaffold |

## Flutter (`flutter test`): 1/1 PASS; `flutter analyze`: No issues found.

## Belum diuji (jujur)

Feature test payroll end-to-end (butuh seed lengkap), approval concurrent race,
webhook signature, Filament browser test, Android/iOS build fisik, load test
payroll 500 karyawan. Alasan: environment/time; bukan diklaim lulus.
