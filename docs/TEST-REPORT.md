# Test Report

- Laravel: **16/16 PASS** (`php artisan test`, SQLite memory).
  - Unit: overtime 6, TER 3, BPJS 3, scaffold 1.
  - Feature: tenant isolation 2, license-gate 1.
- Flutter: **1/1 PASS** (`flutter test`); **analyze: No issues found**.
- Selama penulisan: 2 kegagalan test-driven (kolom NOT NULL `company_id`,
  `gender`; constraint `effective_from`) — diperbaiki di test/seeder, bukan
  dengan melonggarkan kode.
- Belum diuji: e2e payroll penuh, race concurrent, webhook signature,
  browser Filament, build device fisik, load test. Tercatat di
  `docs/payroll/PAYROLL-TEST-SCENARIOS.md` & `docs/audit/07-TEST-COVERAGE.md`.
