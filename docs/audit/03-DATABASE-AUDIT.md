# 03 — Database Audit

## Hasil

- ±95 tabel dari 21 migration. Semua 29 modul ter-cover tabel.
- Uang: 100% `decimal`. Tidak ada float. ✅
- Enum MySQL ±40 kolom — kaku tapi konsisten; SQLite test lolos.

## Temuan & status perbaikan

| ID | Temuan | Severity | Status |
|---|---|---|---|
| T-1 | `reimbursement_requests.business_trip_id` tanpa FK | Tinggi | TERBUKA — butuh migration constrained (ditunda: hindari asumsi travel; catat debt) |
| T-2 | Unique global (assets.code, invoices.number, payments.reference, qr_badge) | Tinggi | TERBUKA — migration composite unique (risiko duplikat existing; butuh cek data) |
| T-3 | Tenant nullable (WA sessions/messages, webhook logs) + scope | Tinggi | DITERIMA — pola ingest pra-tenant sah; didokumentasikan |
| T-4 | FK longgar (head/owner/interviewer json) | Sedang | DITERIMA by-design |
| T-5 | Roles global tanpa teams | Sedang | TERBUKA — mitigasi parsial via BelongsToTenant enforcement |
| T-6 | Index komposit approval kurang | Sedang | TERBUKA (minor) |
| T-7 | SoftDeletes inkonsisten finansial/ATS | Sedang | DITERIMA — kebijakan: run/component terkunci, item turunan cascade |
| T-8 | Missing index (offers, travel, loans, exit, certs, item_components) | Sedang | TERBUKA (perf, bukan correctness) |
| T-9 | Sumber ganda penempatan org | Sedang | DITERIMA — `employees.*` = current, `employee_assignments` = snapshot |
| T-10 | Nomor surat/pinjaman tanpa unique | Rendah | TERBUKA |
| T-11 | `disbursement_items.account_number` plain vs master encrypted | Sedang | TERBUKA — butuh keputusan PDP (mask vs encrypt) |
| T-12 | report_schedules tanpa timezone | Rendah | TERBUKA |

## Relasi baru yang ditambahkan

- `ApprovalRequest::requester()`, `ApprovalRequestStep::approvalRequest()`,
  `TaxCalculation::employee()+payrollItem()`, `BPJSCalculation::employee()+payrollItem()`.

## Aturan migrasi (dipatuhi)

- Tidak ada destructive migration. Satu migration redundan (unique email —
  ternyata sudah ada di bawaan Laravel) langsung dihapus sebelum push.
