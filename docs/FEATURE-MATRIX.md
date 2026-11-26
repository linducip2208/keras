# Feature Matrix

Format: Modul | Fitur | Status awal | Perubahan | Status akhir | Bukti

## Keamanan & tenant

| Fitur | Awal | Perubahan | Akhir | Bukti |
|---|---|---|---|---|
| Tenant binding API | BROKEN (scope mati) | `ResolveApiTenant` post-auth + route group | COMPLETE | `routes/api.php`, `TenantIsolationTest` |
| X-Tenant-ID trust | BROKEN | Dihapus + auth-menang | COMPLETE | `IdentifyTenant.php` |
| Mass-assignment tenant | BROKEN | Enforcement di `creating` | COMPLETE | `BelongsToTenant.php` |
| IDOR leave_type | BROKEN | Tenant-scope + test 404 | COMPLETE | `LeaveController.php` |
| Webhook hardening | PARTIAL | Tolak nonaktif + strip header | PARTIAL | `PaymentWebhookController.php` |

## Payroll & pajak

| Fitur | Awal | Perubahan | Akhir | Bukti |
|---|---|---|---|---|
| PPh TER | BROKEN (kurangi biaya jabatan) | TER langsung ke bruto + test | COMPLETE (formula) | `PayrollCalculationService.php`, `PPh21TerCalculatorTest` |
| Tahun regulasi | BROKEN (`now()`) | `setYear()` dari periode | COMPLETE | idem + `BPJSCalculator` |
| Guard double-approve | MISSING | `refresh()` + test implisit | COMPLETE | `ApprovalEngine.php` |
| Saldo cuti atomic | RACE | `lockForUpdate` + transaksi | COMPLETE | `LeaveController.php` |
| Billing URL | BROKEN 404 | `tokenFor()` invoice | COMPLETE | `BillingService.php` |
| CV screening | BROKEN (tanpa isi) | Baca docx/txt + marker PDF | PARTIAL | `CVScreeningService.php` |
| Slip password | MISLEADING | Dihapus + jujur di kode | PARTIAL (tanpa enkripsi) | `PayslipPdfService.php` |
| Slip number | `now()` | Dari periode payroll | COMPLETE | idem |

## Filament & mobile

| Fitur | Awal | Perubahan | Akhir | Bukti |
|---|---|---|---|---|
| Approve/Reject panel | MISSING | Actions + guard approver | COMPLETE | `ApprovalRequestsTable.php` |
| KeyResults UI | MISSING | RelationManager | COMPLETE | `Objectives/...` |
| Workflow steps UI | MISSING | RelationManager | COMPLETE | `ApprovalWorkflows/...` |
| Riwayat PPh/BPJS UI | MISSING | 2 resource read-only | COMPLETE | `TaxCalculations/`, `BpjsCalculations/` |
| Payroll wizard | EMPTY steps | Guard duplikat + error handling + deskripsi jujur | PARTIAL | `PayrollWizard.php` |
| Mobile cuti form | TODO snackbar | Form penuh + test manual | COMPLETE | `leave_screen.dart` |
| Mobile selfie | FAKE path | Multipart upload + validasi server | COMPLETE | `attendance_screen.dart` + `POST /attendance/photo` |
| Mobile 401 | HANG | Hook logout → login | COMPLETE | `api_client.dart`, `main.dart` |
