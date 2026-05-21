# 05 — Entity Relationship Diagram (ERD)

> Total ~110 tabel di 29 modul + cross-cutting infra. Setiap tabel punya: `id` (bigint PK), `tenant_id` (FK, indexed), `created_at`, `updated_at`, `deleted_at` (soft delete). Kolom standar ini tidak ditulis ulang per tabel kecuali special.

## Konvensi
- **PK**: `id BIGINT UNSIGNED AUTO_INCREMENT`
- **FK naming**: `{entity}_id` (e.g., `employee_id`, `company_id`)
- **Timestamps**: `created_at`, `updated_at` (NULLable di pivot), `deleted_at` (NULLable)
- **Multi-tenant**: `tenant_id BIGINT UNSIGNED NOT NULL`, index per tenant + composite indexes
- **Money**: `DECIMAL(15,2)` (cukup untuk Rp 9 triliun)
- **JSON**: untuk metadata/setting fleksibel
- **Enum**: pakai string + check constraint, hindari MySQL enum (sulit diubah)
- **ULID/UUID**: opsional untuk entity public-facing (e.g., applicant tracking ID)
- **Indeks composite**: kebanyakan `(tenant_id, X)` untuk multi-tenant query

---

## Central / Tenant Management Tables (Global, no tenant_id)

### `tenants`
```
id, slug (unique), name, subdomain (unique), custom_domain (nullable), 
plan (starter|basic|essential|suite|self_host), status (active|suspended|trial|expired), 
trial_ends_at, license_key, db_connection (nullable for DB-per-tenant),
country, timezone, locale, currency, 
billing_email, owner_user_id, settings JSON, 
created_at, updated_at, deleted_at
```
Index: `slug`, `subdomain`, `custom_domain`, `status`

### `tenant_users`
Cross-tenant user mapping (super-admin, partner reseller).
```
id, user_id, tenant_id, role (super_admin|reseller|support), 
created_at, updated_at
```

### `plans`
```
id, slug, name, max_employees, price_monthly DECIMAL(15,2), price_annual,
features JSON, is_active BOOL
```

### `subscriptions`
```
id, tenant_id, plan_id, status, current_period_start, current_period_end,
canceled_at, billing_cycle (monthly|annual), seats INT, 
external_subscription_id (Stripe/midtrans etc.)
```

### `invoices`
```
id, tenant_id, subscription_id, number, period_start, period_end,
amount DECIMAL(15,2), tax_amount, total_amount, status (draft|sent|paid|overdue|void),
paid_at, paid_via, due_date, pdf_path
```

### `system_logs`
Central log untuk admin internal (cross-tenant view).

---

## M01 — Employee Database (12 tables)

### `employees`
```
id, tenant_id, company_id, branch_id, department_id, position_id, job_title_id, job_level_id,
employee_no (unique per tenant, e.g., "EMP-2026-001"),
nik VARCHAR(16) ENCRYPTED, npwp VARCHAR(20) ENCRYPTED,
full_name, nickname, gender (M|F), 
place_of_birth, date_of_birth DATE,
religion (Islam|Christian|Catholic|Hindu|Buddha|Confucian|Other),
marital_status (single|married|divorced|widowed),
ptkp_status (TK_0|TK_1|TK_2|TK_3|K_0|K_1|K_2|K_3|...) — for PPh 21 calc,
blood_type (A|B|AB|O|null),
nationality, ktp_address TEXT, current_address TEXT, postal_code,
phone, phone_alt, personal_email, 
employee_email (kantor),
join_date DATE, probation_end_date DATE, 
employment_status (active|probation|on_leave|suspended|resigned|terminated),
employment_type (permanent|contract|probation|intern|outsource|freelance),
last_working_date DATE,
photo_path, signature_path, qr_badge_code (unique),
manager_employee_id (self-FK), dotted_line_manager_id (self-FK),
custom_fields JSON,
created_at, updated_at, deleted_at
```
Index: `(tenant_id, employee_no)`, `(tenant_id, employment_status)`, `(tenant_id, join_date)`, `nik`, `npwp`

### `employee_families`
```
id, tenant_id, employee_id, relation (spouse|child|parent|sibling), 
name, gender, date_of_birth, nik, is_bpjs_dependent BOOL, is_ptkp_dependent BOOL,
created_at, updated_at
```

### `employee_emergency_contacts`
```
id, tenant_id, employee_id, name, relation, phone, address, 
priority INT, created_at, updated_at
```

### `employee_educations`
```
id, tenant_id, employee_id, level (SD|SMP|SMA|D3|S1|S2|S3),
school_name, major, gpa DECIMAL(3,2), start_year, end_year,
is_highest BOOL, created_at, updated_at
```

### `employee_certifications`
```
id, tenant_id, employee_id, name, issuer, issued_at DATE, 
expires_at DATE (nullable), credential_id, document_path,
created_at, updated_at
```

### `employee_skills`
```
id, tenant_id, employee_id, skill_id, proficiency (beginner|intermediate|advanced|expert), 
years_experience, verified_by_employee_id, created_at, updated_at
```

### `skills` (master, per tenant)
```
id, tenant_id, name, category, created_at, updated_at
```

### `employee_bank_accounts`
```
id, tenant_id, employee_id, bank_name, account_number ENCRYPTED, 
account_holder_name, is_primary BOOL, allocation_percentage DECIMAL(5,2) — for split,
created_at, updated_at
```

### `employee_documents`
```
id, tenant_id, employee_id, category_id, name, file_path, file_size,
mime_type, expires_at DATE (nullable), uploaded_by_user_id,
metadata JSON, created_at, updated_at, deleted_at
```

### `document_categories`
```
id, tenant_id, name, slug, requires_expiry BOOL, alert_days_before INT, 
is_mandatory BOOL, applies_to (employee|company|both)
```

### `custom_fields`
```
id, tenant_id, model_type (employees|contracts|...), field_key, label, 
field_type (text|number|date|select|multi_select|boolean|file),
options JSON (untuk select), is_required, validation_rules JSON, 
display_order, created_at, updated_at
```

### `custom_field_values`
```
id, tenant_id, custom_field_id, model_type, model_id, value TEXT, 
created_at, updated_at
```

---

## M02 — Organization (9 tables)

### `companies` (entity dalam tenant)
```
id, tenant_id, name, legal_name, npwp, npp_bpjs_kesehatan, npp_bpjs_naker,
address, phone, email, logo_path,
parent_company_id (self-FK for group), 
country, timezone, currency, fiscal_year_start_month INT (default 1),
is_default BOOL, created_at, updated_at, deleted_at
```

### `branches`
```
id, tenant_id, company_id, code, name, address, phone, 
latitude DECIMAL(10,8), longitude DECIMAL(11,8), geofence_radius_meters INT,
working_hours JSON (e.g., {"mon":"09:00-18:00"}),
umk_amount DECIMAL(15,2) — UMK kota, untuk BPJS calc,
is_default BOOL, created_at, updated_at, deleted_at
```

### `departments`
```
id, tenant_id, company_id, parent_department_id (self-FK), 
code, name, cost_center_code, headcount_budget INT, 
head_employee_id, created_at, updated_at, deleted_at
```

### `divisions`
```
id, tenant_id, company_id, name, head_employee_id, 
created_at, updated_at
```

### `job_titles`
```
id, tenant_id, name, description, created_at, updated_at
```

### `job_levels`
```
id, tenant_id, code (e.g., L1, L2, ..., L10), name, salary_min, salary_max, 
order INT, created_at, updated_at
```

### `positions` (jabatan unique = job_title + level + dept)
```
id, tenant_id, company_id, department_id, job_title_id, job_level_id,
code, name, headcount_target INT,
created_at, updated_at, deleted_at
```

### `position_history`
```
id, tenant_id, employee_id, position_id, branch_id, department_id,
job_title_id, job_level_id,
effective_from DATE, effective_to DATE (nullable means current),
reason (new_hire|promotion|demotion|mutation|reorg), 
salary_at_change DECIMAL(15,2),
changed_by_user_id, document_id (FK ke hr_letter if any),
created_at, updated_at
```

### `employee_assignments` (current snapshot — denormalized for fast query)
```
id, tenant_id, employee_id (unique), company_id, branch_id, department_id, 
position_id, job_title_id, job_level_id, manager_employee_id,
effective_since DATE, created_at, updated_at
```

---

## M03 — Contract & Documents (5 tables)

### `contracts`
```
id, tenant_id, employee_id, contract_type (PKWT|PKWTT|probation|intern|outsource|freelance|daily),
contract_no (unique per tenant), template_id (FK contract_templates, nullable),
start_date DATE, end_date DATE (nullable for PKWTT),
probation_end_date DATE,
salary_base DECIMAL(15,2), salary_currency,
job_title_id, position_id, department_id, branch_id,
working_hours_per_week INT,
status (draft|active|expired|terminated|renewed),
parent_contract_id (FK self, untuk track renewal chain),
file_path (signed PDF),
signed_at TIMESTAMP, signed_by_employee_at, signed_by_company_at,
e_sign_provider, e_sign_reference_id,
notes TEXT, created_at, updated_at, deleted_at
```

### `contract_templates`
```
id, tenant_id, name, contract_type, body_template TEXT, 
variables JSON (list of {name, label, type}),
is_active BOOL, created_at, updated_at, deleted_at
```

### `contract_renewals` (audit dari renewal events)
```
id, tenant_id, old_contract_id, new_contract_id, decided_by_user_id,
decided_at, reason, created_at
```

### `document_expiry_alerts`
```
id, tenant_id, document_id, alert_at DATE, sent_at, notified_user_ids JSON,
created_at, updated_at
```

### `documents` (already defined in M01 — generic)
— shared dengan `employee_documents`; tabel `documents` versi general untuk company-level docs

---

## M04 — HR Letter (4 tables)

### `hr_letters`
```
id, tenant_id, employee_id, letter_type (sk|paklaring|sp1|sp2|sp3|spd|mutasi|promosi|demosi|custom),
template_id, letter_no (unique per tenant per type), letter_date DATE,
variables_data JSON, body_rendered TEXT, file_path,
status (draft|finalized|signed|delivered|archived),
created_by_user_id, signed_at, delivered_at, 
e_sign_provider, e_sign_reference_id,
created_at, updated_at, deleted_at
```

### `hr_letter_templates`
```
id, tenant_id, letter_type, name, body_template TEXT, 
variables JSON, default_signer_role (e.g., "HR Manager"), 
is_active, created_at, updated_at, deleted_at
```

### `hr_letter_number_counters`
```
id, tenant_id, letter_type, year, sequence_format (e.g., "{TYPE}/{YEAR}/{MONTH}/{SEQ:4}"),
last_sequence INT, created_at, updated_at
UNIQUE(tenant_id, letter_type, year)
```

### `hr_letter_bulk_jobs`
Untuk bulk generate (e.g., 200 SK kenaikan).
```
id, tenant_id, template_id, parameters JSON, total_count, completed_count,
status (queued|running|completed|failed), created_by_user_id,
created_at, updated_at
```

---

## M05 — Approval Workflow (5 tables)

### `approval_workflows`
```
id, tenant_id, slug, name, applies_to (leave|overtime|reimburse|travel|loan|attendance|custom),
conditions JSON (e.g., {"min_amount":1000000, "dept":"sales"}),
is_active BOOL, priority INT, created_at, updated_at, deleted_at
```

### `approval_workflow_steps`
```
id, tenant_id, workflow_id, order INT, name, 
approver_type (employee|role|position|manager|manager_of_manager|hr|finance|custom),
approver_id (nullable, depends on type),
approval_mode (single|all|any), timeout_hours INT,
escalation_step_id (FK self, nullable),
is_optional BOOL, created_at, updated_at
```

### `approval_requests`
```
id, tenant_id, workflow_id, requestable_type (leave_requests|overtime_requests|...),
requestable_id, requester_employee_id,
status (pending|approved|rejected|canceled|escalated),
current_step_order INT, total_steps INT,
submitted_at, completed_at, created_at, updated_at
```

### `approval_request_steps`
```
id, tenant_id, approval_request_id, step_order INT,
approver_employee_id, action (pending|approved|rejected|delegated|escalated),
comment TEXT, attachment_path, acted_at, due_at, 
delegated_to_employee_id (nullable), created_at, updated_at
```

### `approval_delegations`
```
id, tenant_id, delegator_employee_id, delegate_employee_id,
start_date, end_date, reason, created_at, updated_at
```

---

## M06 — Announcement (5 tables)

### `announcements`
```
id, tenant_id, title, body TEXT, attachment_paths JSON,
created_by_user_id, segment JSON (e.g., {"dept":[1,2], "branch":[1]}),
publish_at, expire_at, is_pinned BOOL, priority INT,
status (draft|published|expired),
created_at, updated_at, deleted_at
```

### `announcement_recipients`
```
id, tenant_id, announcement_id, employee_id, 
notified_at, read_at, acknowledged_at, created_at, updated_at
UNIQUE(announcement_id, employee_id)
```

### `announcement_polls`
```
id, tenant_id, announcement_id, question, options JSON,
is_multi_select BOOL, is_anonymous BOOL, closes_at,
created_at, updated_at
```

### `announcement_poll_responses`
```
id, tenant_id, poll_id, employee_id, selected_options JSON, 
created_at, updated_at
UNIQUE(poll_id, employee_id) — kecuali multi-select beda mekanisme
```

### `notification_preferences`
```
id, tenant_id, employee_id, event_type, 
channels JSON (e.g., ["push","email","whatsapp"]),
is_muted BOOL, created_at, updated_at
UNIQUE(employee_id, event_type)
```

---

## M07 — ESS (no new core tables — reuses above; only `user_devices`)

### `user_devices`
```
id, tenant_id, user_id, employee_id, 
device_type (ios|android|web), device_token (FCM/APNs), 
device_id, app_version, last_active_at, created_at, updated_at
```

---

## M08 — Digital Signature (3 tables)

### `signature_requests`
```
id, tenant_id, signable_type (hr_letters|contracts|...), signable_id,
provider (bsre|privy|custom), provider_request_id,
status (pending|signing|completed|expired|canceled|failed),
expires_at, completed_at, created_at, updated_at
```

### `signature_request_signers`
```
id, tenant_id, signature_request_id, signer_employee_id, signer_email,
signer_name, signed_at, signature_path, certificate_path,
order INT, status (pending|signed|declined),
created_at, updated_at
```

### `signature_provider_configs`
```
id, tenant_id, provider_slug, config JSON ENCRYPTED, 
is_active, created_at, updated_at
```

---

## M09 — Shift & Schedule (5 tables)

### `shifts`
```
id, tenant_id, code, name, start_time TIME, end_time TIME,
break_minutes INT, working_hours DECIMAL(4,2),
crosses_midnight BOOL, allowed_late_minutes INT, allowed_early_leave_minutes INT,
color, is_off_day BOOL, created_at, updated_at, deleted_at
```

### `shift_patterns`
Pattern siklus (e.g., 6-1, 5-2).
```
id, tenant_id, name, days_in_cycle INT, 
shift_sequence JSON (e.g., [{day:1, shift_id:1}, {day:2, shift_id:1}, ...]),
created_at, updated_at, deleted_at
```

### `schedule_templates`
```
id, tenant_id, name, applies_to_dept_id (nullable), applies_to_position_id (nullable),
shift_pattern_id, created_at, updated_at, deleted_at
```

### `employee_schedules`
```
id, tenant_id, employee_id, date DATE, shift_id (nullable for off day),
is_off_day BOOL, source (template|manual|swap),
notes TEXT, created_at, updated_at
UNIQUE(employee_id, date)
INDEX (tenant_id, date)
```

### `shift_swap_requests`
```
id, tenant_id, requester_employee_id, target_employee_id, 
requester_date, target_date, requester_shift_id, target_shift_id,
status (pending|target_accepted|approved|rejected|canceled),
target_accepted_at, manager_action_at, approval_request_id,
created_at, updated_at
```

---

## M10 — Attendance (6 tables)

### `attendance_logs`
```
id, tenant_id, employee_id, date DATE, shift_id, 
check_in_at TIMESTAMP, check_in_latitude, check_in_longitude, 
check_in_address TEXT, check_in_photo_path, check_in_method (gps|fingerprint|qr|face|manual),
check_in_device_id, check_in_branch_id (nullable, for multi-branch),
check_in_is_late BOOL, check_in_late_minutes INT,
check_in_is_mock_gps BOOL,

check_out_at TIMESTAMP, check_out_latitude, check_out_longitude,
check_out_address, check_out_photo_path, check_out_method,
check_out_device_id, check_out_branch_id,
check_out_is_early BOOL, check_out_early_minutes INT,
check_out_is_mock_gps BOOL,

worked_minutes INT, overtime_minutes INT,
status (present|absent|leave|half_day|holiday|weekend),
notes TEXT, created_at, updated_at
INDEX (tenant_id, employee_id, date), (tenant_id, date)
UNIQUE(employee_id, date)
```

### `attendance_corrections`
```
id, tenant_id, attendance_log_id (nullable for missing), employee_id, date,
requested_check_in_at, requested_check_out_at, reason TEXT,
attachment_path, approval_request_id,
status (pending|approved|rejected), applied_at,
created_at, updated_at
```

### `geofence_zones`
Selain branch default — bisa ada project site, customer site.
```
id, tenant_id, branch_id (nullable), name, latitude, longitude, 
radius_meters, valid_from, valid_to, created_at, updated_at
```

### `fingerprint_devices`
```
id, tenant_id, branch_id, vendor (zkteco|suprema|other), model, ip_address, 
serial_number, last_sync_at, status (online|offline), 
api_config JSON ENCRYPTED, created_at, updated_at
```

### `face_templates` (Phase 5+)
```
id, tenant_id, employee_id, template_vector BLOB ENCRYPTED, 
quality_score DECIMAL(3,2), captured_at, created_at, updated_at
```

### `attendance_settings` (per tenant + per branch override)
```
id, tenant_id, branch_id (nullable for company-wide), 
allow_mock_gps_clock_in BOOL, require_selfie BOOL, require_face_recognition BOOL,
late_tolerance_minutes INT, auto_clock_out_minutes INT, 
allow_offline_clock_in BOOL, max_offline_hours INT,
created_at, updated_at
```

---

## M11 — Overtime (3 tables)

### `overtime_requests`
```
id, tenant_id, employee_id, date DATE, planned_start TIME, planned_end TIME,
actual_start TIMESTAMP, actual_end TIMESTAMP, planned_hours DECIMAL(4,2),
actual_hours DECIMAL(4,2), reason TEXT, attachment_path,
overtime_type (workday|weekend|holiday),
approval_request_id, status (pending|approved|rejected|completed),
amount_calculated DECIMAL(15,2), use_compensatory_leave BOOL,
created_at, updated_at, deleted_at
```

### `overtime_rules` (per company / per role)
```
id, tenant_id, company_id, position_id (nullable, default if null),
basis (kepmenaker_102|custom), 
workday_rates JSON (e.g., {"hour_1":1.5, "hour_2_plus":2}),
holiday_rates JSON, hour_divisor INT (default 173),
max_hours_per_day INT, max_hours_per_week INT, 
is_active BOOL, created_at, updated_at, deleted_at
```

### `overtime_logs` (denormalized aggregat untuk payroll)
```
id, tenant_id, employee_id, payroll_period_id, 
total_hours_workday DECIMAL(6,2), total_hours_holiday DECIMAL(6,2),
total_amount DECIMAL(15,2), created_at, updated_at
```

---

## M12 — Leave (6 tables)

### `leave_types`
```
id, tenant_id, code, name, days_per_year DECIMAL(5,2) (nullable for unlimited),
is_paid BOOL, requires_attachment BOOL, requires_balance BOOL,
gender_specific (M|F|null), max_consecutive_days INT,
min_notice_days INT, can_be_half_day BOOL, 
can_carry_over BOOL, carry_over_max_days, carry_over_expires_in_months INT,
deducts_from JSON (e.g., ["annual"] kalau cuti bersama deduct dari annual),
is_active, created_at, updated_at, deleted_at
```

### `leave_balances`
```
id, tenant_id, employee_id, leave_type_id, year INT,
allocated DECIMAL(5,2), used DECIMAL(5,2), carried_over DECIMAL(5,2),
pending DECIMAL(5,2) — yang sedang request approval,
expires_at DATE, created_at, updated_at
UNIQUE(employee_id, leave_type_id, year)
```

### `leave_requests`
```
id, tenant_id, employee_id, leave_type_id, 
start_date, end_date, days_count DECIMAL(5,2), is_half_day BOOL, half_day_period (am|pm),
reason TEXT, attachment_path, contact_during_leave,
delegate_employee_id (nullable), 
approval_request_id, status (pending|approved|rejected|canceled),
applied_at, canceled_at, created_at, updated_at, deleted_at
```

### `leave_accruals` (transactional log per accrual event)
```
id, tenant_id, employee_id, leave_type_id, accrual_date, 
amount DECIMAL(5,2), reason (monthly_accrual|annual_grant|carry_over|adjustment),
created_by_user_id, notes, created_at, updated_at
```

### `leave_carry_overs`
```
id, tenant_id, employee_id, leave_type_id, from_year, to_year,
amount DECIMAL(5,2), expires_at DATE, status (active|expired|used),
created_at, updated_at
```

### `leave_policies`
```
id, tenant_id, company_id, leave_type_id, applies_to_position_id (nullable),
accrual_method (monthly|quarterly|annual|tenure_based), 
accrual_amount DECIMAL(5,2), accrual_rules JSON,
min_tenure_months_to_use INT, created_at, updated_at
```

---

## M13 — Holiday (3 tables)

### `holidays` (master Indonesia, global)
```
id, country_code, year, date DATE, name, type (national|religious|cuti_bersama),
religion (nullable), is_recurring BOOL (Lebaran tiap tahun beda tanggal — false), 
created_at, updated_at
```

### `holiday_calendars` (per tenant override)
```
id, tenant_id, company_id (nullable for tenant-wide), branch_id (nullable for company-wide),
holiday_id (nullable jika custom), date DATE, name, is_working_day_override BOOL,
created_at, updated_at
```

### `holiday_overrides`
Tenant can disable certain holidays (e.g., kerja shift jadi tidak libur).
```
id, tenant_id, branch_id, holiday_date, is_observed BOOL, notes,
created_at, updated_at
```

---

## M14 — Payroll (9 tables)

### `payroll_periods`
```
id, tenant_id, company_id, name (e.g., "2026-05"), period_type (monthly|weekly|biweekly|daily),
start_date, end_date, cutoff_attendance_date, payroll_date, 
status (open|cutoff|processing|finalized|paid|locked), 
created_at, updated_at
```

### `payroll_runs`
```
id, tenant_id, payroll_period_id, run_type (regular|off_cycle|bonus|thr|correction),
status (draft|calculating|review|approved|disbursed|finalized|failed),
total_employees INT, total_gross DECIMAL(15,2), total_net DECIMAL(15,2),
total_tax DECIMAL(15,2), total_bpjs_employer DECIMAL(15,2), total_bpjs_employee DECIMAL(15,2),
started_at, completed_at, approved_by_user_id, approved_at,
created_by_user_id, created_at, updated_at, deleted_at
```

### `payroll_components` (master per tenant)
```
id, tenant_id, code, name, type (earning|deduction|benefit),
calculation (fixed|formula|attendance_based|manual),
formula TEXT, default_amount DECIMAL(15,2),
is_taxable BOOL, is_bpjs_basis BOOL (counted in BPJS calculation basis),
is_thr_basis BOOL, is_pension_basis BOOL,
display_order, is_active, created_at, updated_at, deleted_at
```

### `payroll_items` (per employee per run)
```
id, tenant_id, payroll_run_id, employee_id,
basic_salary DECIMAL(15,2), gross DECIMAL(15,2), 
total_earnings DECIMAL(15,2), total_deductions DECIMAL(15,2),
total_benefits DECIMAL(15,2),
pph_21 DECIMAL(15,2), bpjs_employer DECIMAL(15,2), bpjs_employee DECIMAL(15,2),
net_pay DECIMAL(15,2), 
working_days INT, present_days INT, absent_days INT, leave_days INT,
overtime_hours DECIMAL(6,2), overtime_amount DECIMAL(15,2),
prorate_days INT, prorate_factor DECIMAL(5,4) (0.0000-1.0000),
status (pending|calculated|approved|paid|hold), 
hold_reason, calculated_at, created_at, updated_at
UNIQUE(payroll_run_id, employee_id)
```

### `payroll_item_components` (line items per payroll_item)
```
id, tenant_id, payroll_item_id, payroll_component_id, 
amount DECIMAL(15,2), quantity DECIMAL(8,2) (e.g., 22 hari makan),
unit_price DECIMAL(15,2), 
formula_used TEXT, calculation_breakdown JSON,
is_taxable BOOL, is_bpjs_basis BOOL, 
created_at, updated_at
```

### `payslips`
```
id, tenant_id, payroll_item_id, employee_id, 
slip_number, slip_date, pdf_path,
sent_email_at, sent_whatsapp_at, opened_at, downloaded_at,
password_hint, created_at, updated_at
```

### `payroll_formulas` (formula library reusable)
```
id, tenant_id, slug, name, formula TEXT, variables JSON, 
description, created_at, updated_at
```

### `payroll_approvals`
```
id, tenant_id, payroll_run_id, approver_role, approver_user_id, 
action (approved|rejected), comment, acted_at, created_at, updated_at
```

### `payroll_holds` (untuk hold karyawan tertentu)
```
id, tenant_id, payroll_item_id, reason, hold_by_user_id, hold_at,
released_at, created_at, updated_at
```

---

## M15 — Tax PPh 21 (6 tables)

### `tax_settings`
```
id, tenant_id, company_id, npwp, klu_code (Klasifikasi Lapangan Usaha),
calculation_method (gross|nett|gross_up_thr), 
ptkp_year INT, ter_year INT, is_active, created_at, updated_at
```

### `ptkp_rules` (master)
```
id, year INT, status (TK_0|TK_1|...|K_3), amount DECIMAL(15,2),
description, created_at, updated_at
UNIQUE(year, status)
```

### `ter_rates` (TER 2024 brackets — master, per year)
```
id, year, category (A|B|C — by PTKP), bracket_from DECIMAL(15,2), bracket_to DECIMAL(15,2),
rate_percent DECIMAL(5,2), created_at, updated_at
```

### `tax_calculations` (per payroll item)
```
id, tenant_id, payroll_item_id, employee_id, period_year, period_month,
gross_basis DECIMAL(15,2), ptkp_amount DECIMAL(15,2),
ter_category, ter_rate DECIMAL(5,2),
pph_21_amount DECIMAL(15,2), method_used (ter|gross|nett|gross_up),
calculation_breakdown JSON,
created_at, updated_at
```

### `tax_forms` (1721-A1, 1721-I generated docs)
```
id, tenant_id, employee_id (nullable for company-wide), 
form_type (1721_a1|1721_i|1721_a2|other), period_year, period_month,
file_path, status (draft|finalized|submitted), submitted_at, 
djp_reference_id, created_at, updated_at
```

### `tax_periods`
```
id, tenant_id, company_id, year, month, status (open|closed|submitted), 
total_employees INT, total_gross, total_tax, submitted_at, file_1721_i_path,
created_at, updated_at
UNIQUE(company_id, year, month)
```

---

## M16 — BPJS (7 tables)

### `bpjs_company_profiles`
```
id, tenant_id, company_id, npp_bpjs_kesehatan, npp_bpjs_naker, 
jkk_risk_level (very_low|low|medium|high|very_high), jkk_rate DECIMAL(5,4),
bpjs_branch_kesehatan, bpjs_branch_naker, 
created_at, updated_at
```

### `bpjs_rates` (master per tahun, governmental rate)
```
id, year, effective_from DATE, effective_to DATE, 
program (kesehatan|jht|jp|jkk|jkm),
employer_rate DECIMAL(5,4), employee_rate DECIMAL(5,4),
salary_cap DECIMAL(15,2), notes, created_at, updated_at
```

### `bpjs_enrollments`
```
id, tenant_id, employee_id, program (kesehatan|jht|jp|jkk|jkm),
bpjs_number ENCRYPTED, enrolled_at DATE, terminated_at DATE, 
status (active|suspended|terminated), created_at, updated_at
UNIQUE(employee_id, program)
```

### `bpjs_dependents` (untuk BPJS Kesehatan)
```
id, tenant_id, employee_id, family_id (FK employee_families), 
bpjs_number ENCRYPTED, enrolled_at, terminated_at, 
status, created_at, updated_at
```

### `bpjs_calculations`
```
id, tenant_id, payroll_item_id, employee_id, program,
basis_amount DECIMAL(15,2), employer_amount DECIMAL(15,2), 
employee_amount DECIMAL(15,2), created_at, updated_at
```

### `bpjs_sipp_exports`
```
id, tenant_id, company_id, period_year, period_month, 
program, file_path, status (generated|uploaded|processed),
uploaded_at, created_at, updated_at
```

### `bpjs_claims`
```
id, tenant_id, employee_id, program (jht|jkk|jkm|kesehatan),
claim_type, claim_amount, status (draft|submitted|approved|paid|rejected),
submitted_at, processed_at, bpjs_reference_id, 
documents JSON, created_at, updated_at
```

---

## M17 — Disbursement (4 tables)

### `disbursement_runs`
```
id, tenant_id, payroll_run_id, 
disbursement_method (batch_file|instant|manual), 
adapter_slug, total_amount DECIMAL(15,2), total_count INT,
status (draft|processing|completed|partially_completed|failed),
file_path, executed_at, completed_at, created_at, updated_at
```

### `disbursement_items`
```
id, tenant_id, disbursement_run_id, employee_id, bank_account_id, 
amount DECIMAL(15,2), bank_code, bank_name, account_number, account_holder,
status (pending|sent|completed|failed|reversed),
external_reference_id, error_message, processed_at, 
created_at, updated_at
```

### `disbursement_files`
```
id, tenant_id, disbursement_run_id, bank_format (bca|mandiri|bni|bri|cimb),
file_path, generated_at, downloaded_at, downloaded_by_user_id,
proof_upload_path, created_at, updated_at
```

### `bank_adapter_configs` (dinamis per tenant)
```
id, tenant_id, name (user input), adapter_slug, config JSON ENCRYPTED,
is_active, created_at, updated_at
```

---

## M18 — Reimbursement (5 tables)

### `reimbursement_categories`
```
id, tenant_id, name, slug, max_per_request DECIMAL(15,2), 
requires_receipt BOOL, requires_approval BOOL,
gl_account_code, is_active, created_at, updated_at, deleted_at
```

### `reimbursement_policies`
```
id, tenant_id, category_id, applies_to (company|department|position|employee_group),
applies_to_id (nullable), limit_amount DECIMAL(15,2), 
limit_period (day|month|quarter|year), 
created_at, updated_at, deleted_at
```

### `reimbursement_budgets`
```
id, tenant_id, department_id, category_id, year, month (nullable for annual),
budget_amount DECIMAL(15,2), used_amount DECIMAL(15,2), 
created_at, updated_at
```

### `reimbursement_requests`
```
id, tenant_id, employee_id, request_no, 
total_amount DECIMAL(15,2), currency, 
purpose TEXT, business_trip_id (FK travel_requests, nullable),
approval_request_id, status (draft|submitted|approved|rejected|paid|canceled),
disbursement_method (payroll|separate), disbursement_run_id,
submitted_at, approved_at, paid_at, created_at, updated_at, deleted_at
```

### `reimbursement_items`
```
id, tenant_id, request_id, category_id, item_date DATE,
description, amount DECIMAL(15,2), receipt_path, 
ocr_data JSON, vendor_name, 
created_at, updated_at
```

---

## M19 — Recruitment / ATS (10 tables)

### `job_postings`
```
id, tenant_id, company_id, department_id, position_id (nullable),
title, slug, description LONGTEXT, requirements LONGTEXT,
location, employment_type, salary_min, salary_max, salary_currency,
status (draft|published|closed|filled),
published_at, closed_at, posted_by_user_id,
external_links JSON (LinkedIn, Jobstreet IDs), 
public_token (untuk URL public),
created_at, updated_at, deleted_at
```

### `candidates`
```
id, tenant_id, full_name, email, phone, 
linkedin_url, portfolio_url,
location, current_position, years_experience,
cv_path, photo_path, ai_summary TEXT, ai_score DECIMAL(5,2),
talent_pool_tags JSON, source (career_page|linkedin|referral|jobstreet|manual),
referred_by_employee_id, 
created_at, updated_at, deleted_at
```

### `applications`
```
id, tenant_id, candidate_id, job_posting_id, 
current_stage_id, ai_match_score DECIMAL(5,2),
cover_letter TEXT, salary_expectation DECIMAL(15,2),
notice_period_days INT, status (active|hired|rejected|withdrawn|on_hold),
hired_employee_id (nullable, FK setelah hire),
rejected_reason, withdrawn_reason,
applied_at, created_at, updated_at, deleted_at
UNIQUE(candidate_id, job_posting_id)
```

### `application_stages` (master per tenant)
```
id, tenant_id, name, order INT, color, is_terminal BOOL,
created_at, updated_at, deleted_at
```

### `application_stage_history`
```
id, tenant_id, application_id, from_stage_id, to_stage_id,
moved_by_user_id, moved_at, notes, created_at
```

### `interviews`
```
id, tenant_id, application_id, interview_type (phone|video|onsite|technical|hr|panel),
scheduled_at, duration_minutes, location, meeting_link,
interviewer_employee_ids JSON, status (scheduled|completed|canceled|no_show),
created_by_user_id, created_at, updated_at
```

### `interview_feedbacks`
```
id, tenant_id, interview_id, interviewer_employee_id, 
overall_rating INT (1-5), recommendation (strong_yes|yes|maybe|no|strong_no),
strengths TEXT, weaknesses TEXT, notes TEXT,
submitted_at, created_at, updated_at
```

### `offers`
```
id, tenant_id, application_id, candidate_id, 
position_id, salary_offered DECIMAL(15,2), other_benefits TEXT,
offer_letter_path, start_date DATE,
status (draft|sent|accepted|declined|withdrawn|expired),
sent_at, responded_at, e_sign_request_id,
created_at, updated_at
```

### `referrals`
```
id, tenant_id, referrer_employee_id, candidate_id, 
job_posting_id (nullable), bonus_amount DECIMAL(15,2),
bonus_paid_at, status (pending|active|hired|rejected|paid),
created_at, updated_at
```

### `candidate_communications`
Log email/WA blast.
```
id, tenant_id, candidate_id, channel (email|whatsapp|sms),
direction (outbound|inbound), subject, body TEXT, 
sent_at, opened_at, replied_at, sent_by_user_id,
created_at, updated_at
```

---

## M20 — Onboarding (4 tables)

### `onboarding_templates`
```
id, tenant_id, name, applies_to_position_id (nullable), 
applies_to_department_id (nullable), 
duration_days INT, created_at, updated_at, deleted_at
```

### `onboarding_template_tasks`
```
id, tenant_id, template_id, order INT, name, description,
day_offset INT (e.g., +1, +7, +30), 
task_type (document_upload|training|meeting|signature|asset_assignment|custom),
assigned_role (new_hire|hr|manager|it|ga|buddy), is_mandatory BOOL,
created_at, updated_at
```

### `onboarding_processes`
```
id, tenant_id, employee_id, template_id, buddy_employee_id,
start_date, end_date_planned, end_date_actual,
status (in_progress|completed|abandoned), 
created_at, updated_at
```

### `onboarding_tasks` (per process)
```
id, tenant_id, process_id, template_task_id, 
name, due_at, assigned_to_employee_id, 
status (pending|in_progress|completed|skipped), completed_at,
notes, attachment_path, created_at, updated_at
```

### `probation_reviews`
```
id, tenant_id, employee_id, review_at_days (30|60|90), 
review_date DATE, reviewer_employee_id, 
rating (excellent|good|needs_improvement|unsatisfactory),
feedback TEXT, decision (extend_probation|confirm_permanent|terminate),
created_at, updated_at
```

---

## M21 — OKR & KPI (5 tables)

### `okr_cycles`
```
id, tenant_id, company_id, name (e.g., "Q3 2026"), 
start_date, end_date, status (planning|active|closed|archived),
created_at, updated_at, deleted_at
```

### `objectives`
```
id, tenant_id, cycle_id, owner_employee_id (nullable for company), 
parent_objective_id (cascading), 
title, description, weight DECIMAL(5,2) (untuk KPI),
progress_percent DECIMAL(5,2), 
is_public BOOL, status (draft|active|completed|canceled),
created_at, updated_at, deleted_at
```

### `key_results`
```
id, tenant_id, objective_id, title, description,
metric_type (number|percentage|currency|boolean), 
start_value DECIMAL(15,2), target_value DECIMAL(15,2), current_value DECIMAL(15,2),
unit, progress_percent DECIMAL(5,2),
created_at, updated_at, deleted_at
```

### `okr_check_ins`
```
id, tenant_id, objective_id (or key_result_id), employee_id,
check_in_date, progress_update TEXT, confidence_level (1-5),
blockers TEXT, current_value DECIMAL(15,2), created_at, updated_at
```

### `okr_alignments`
```
id, tenant_id, child_objective_id, parent_objective_id,
contribution_weight DECIMAL(5,2), created_at, updated_at
```

---

## M22 — Performance Appraisal (8 tables)

### `appraisal_cycles`
```
id, tenant_id, name, cycle_type (annual|semester|quarterly),
start_date, end_date, self_review_start, self_review_end,
peer_review_start, peer_review_end, manager_review_start, manager_review_end,
calibration_date, finalization_date,
status (planning|active|review|calibration|completed),
created_at, updated_at, deleted_at
```

### `appraisal_forms`
```
id, tenant_id, name, applies_to_position_id (nullable), 
applies_to_level_id (nullable), is_active, 
created_at, updated_at, deleted_at
```

### `appraisal_questions`
```
id, tenant_id, form_id, section, order INT, 
question_type (rating|text|multi_choice|competency_rating),
question TEXT, options JSON, scale_min INT, scale_max INT,
is_required BOOL, weight DECIMAL(5,2), competency_id (nullable),
created_at, updated_at
```

### `appraisal_assignments`
```
id, tenant_id, cycle_id, subject_employee_id, reviewer_employee_id,
reviewer_type (self|peer|manager|subordinate|skip_level),
form_id, status (pending|in_progress|submitted), submitted_at,
created_at, updated_at
UNIQUE(cycle_id, subject_employee_id, reviewer_employee_id)
```

### `appraisal_responses`
```
id, tenant_id, assignment_id, question_id, 
answer_text TEXT, answer_rating INT, answer_choice, 
created_at, updated_at
```

### `appraisal_ratings` (aggregated final rating)
```
id, tenant_id, cycle_id, employee_id, 
self_score DECIMAL(5,2), peer_score DECIMAL(5,2), 
manager_score DECIMAL(5,2), subordinate_score DECIMAL(5,2),
final_score DECIMAL(5,2), final_rating (e.g., "Exceeds Expectations"),
calibrated BOOL, calibrated_at, calibrated_by_user_id,
notes TEXT, created_at, updated_at
UNIQUE(cycle_id, employee_id)
```

### `nine_box_placements`
```
id, tenant_id, cycle_id, employee_id, 
performance_quadrant INT (1-3), potential_quadrant INT (1-3),
box_code (e.g., "9", "8", "7"), placement_notes TEXT,
created_at, updated_at
UNIQUE(cycle_id, employee_id)
```

### `continuous_feedbacks` (kudos / instant feedback)
```
id, tenant_id, from_employee_id, to_employee_id,
feedback_type (kudos|constructive|peer_recognition),
visibility (public|private|manager_only), message TEXT,
related_competency_id (nullable), is_anonymous BOOL,
created_at, updated_at
```

---

## M23 — Competency & Succession (6 tables)

### `competencies`
```
id, tenant_id, name, description, category, 
is_core BOOL, is_active, created_at, updated_at, deleted_at
```

### `competency_levels`
```
id, tenant_id, competency_id, level INT (1-5 or 1-10), 
name (e.g., "Beginner", "Intermediate"), description TEXT,
behavioral_indicators TEXT, created_at, updated_at
```

### `role_competencies`
```
id, tenant_id, position_id, competency_id, required_level INT, 
weight DECIMAL(5,2), is_critical BOOL, created_at, updated_at
```

### `employee_competencies` (current assessment)
```
id, tenant_id, employee_id, competency_id, current_level INT,
assessed_at, assessed_by_employee_id, 
created_at, updated_at
UNIQUE(employee_id, competency_id)
```

### `career_paths`
```
id, tenant_id, from_position_id, to_position_id, 
typical_years INT, required_competencies JSON, 
created_at, updated_at
```

### `succession_plans`
```
id, tenant_id, critical_position_id, 
successor_employee_id, readiness (ready_now|1_2_years|3_5_years|long_term),
risk_if_vacant (low|medium|high|critical),
notes TEXT, last_reviewed_at, created_at, updated_at
```

### `idp_plans` (Individual Development Plan)
```
id, tenant_id, employee_id, year, focus_competencies JSON, 
development_actions JSON (course IDs, training, mentor),
target_completion DATE, status (active|completed|abandoned),
created_at, updated_at
```

---

## M24 — LMS (8 tables)

### `courses`
```
id, tenant_id, slug, title, description, category, 
duration_minutes INT, level (beginner|intermediate|advanced),
thumbnail_path, instructor_employee_id (nullable),
is_mandatory_for_roles JSON (position_ids),
passing_score INT (untuk quiz), certificate_template_id (nullable),
is_published BOOL, published_at, created_at, updated_at, deleted_at
```

### `course_modules`
```
id, tenant_id, course_id, order INT, title, 
content_type (video|pdf|scorm|link|text|quiz),
content_url, content_path, duration_minutes INT,
is_mandatory BOOL, created_at, updated_at
```

### `quizzes`
```
id, tenant_id, course_id, module_id (nullable, can be standalone),
title, time_limit_minutes INT, passing_score INT, 
allow_retake BOOL, max_attempts INT,
created_at, updated_at
```

### `quiz_questions`
```
id, tenant_id, quiz_id, order INT, 
question_type (single_choice|multi_choice|true_false|fill_blank),
question TEXT, options JSON, correct_answer JSON, 
explanation TEXT, points INT, created_at, updated_at
```

### `enrollments`
```
id, tenant_id, employee_id, course_id, 
enrolled_at, enrolled_by_user_id, due_date, 
status (enrolled|in_progress|completed|failed|expired),
progress_percent DECIMAL(5,2), completed_at, 
created_at, updated_at
UNIQUE(employee_id, course_id)
```

### `course_completions` (per module progress)
```
id, tenant_id, enrollment_id, module_id, 
completed_at, time_spent_minutes INT, score INT (nullable for non-quiz),
created_at, updated_at
```

### `quiz_attempts`
```
id, tenant_id, enrollment_id, quiz_id, 
started_at, submitted_at, score INT, passed BOOL,
answers JSON, attempt_number INT,
created_at, updated_at
```

### `certificates`
```
id, tenant_id, employee_id, course_id (or external), 
certificate_no, issued_at, expires_at (nullable),
certificate_path, issued_by_employee_id, 
created_at, updated_at
```

### `learning_paths`
```
id, tenant_id, name, description, target_position_id (nullable),
courses_in_order JSON (course IDs), 
is_active, created_at, updated_at, deleted_at
```

---

## M25 — Asset (5 tables)

### `asset_categories`
```
id, tenant_id, name, depreciation_method (straight_line|none),
useful_life_years INT, gl_account_code,
created_at, updated_at, deleted_at
```

### `assets`
```
id, tenant_id, category_id, code (unique), name, 
brand, model, serial_number, color, 
purchase_date, purchase_amount DECIMAL(15,2), supplier, 
warranty_until DATE, current_value DECIMAL(15,2),
condition (new|good|fair|damaged|written_off),
location, status (in_stock|assigned|maintenance|disposed|lost),
photo_path, notes TEXT,
created_at, updated_at, deleted_at
```

### `asset_assignments`
```
id, tenant_id, asset_id, employee_id, 
assigned_at TIMESTAMP, assigned_by_user_id,
returned_at TIMESTAMP, returned_to_user_id, 
condition_at_assign, condition_at_return,
handover_form_path, return_form_path,
status (active|returned|lost|damaged),
created_at, updated_at
```

### `asset_maintenances`
```
id, tenant_id, asset_id, type (preventive|repair|upgrade),
scheduled_at, performed_at, vendor, cost DECIMAL(15,2),
notes, attachment_path, created_at, updated_at
```

### `asset_requests`
```
id, tenant_id, employee_id, asset_category_id, 
quantity INT, reason TEXT, urgency (low|normal|high),
approval_request_id, status (pending|approved|fulfilled|rejected),
fulfilled_asset_ids JSON, created_at, updated_at
```

---

## M26 — Travel/SPPD (5 tables)

### `travel_requests`
```
id, tenant_id, employee_id, request_no,
origin, destination, purpose TEXT, 
start_date DATE, end_date DATE, 
transport_mode (flight|train|car|bus|other),
estimated_cost DECIMAL(15,2),
approval_request_id, sppd_letter_id (FK hr_letters),
status (draft|approved|in_progress|completed|canceled),
created_at, updated_at, deleted_at
```

### `travel_advances`
```
id, tenant_id, travel_request_id, amount DECIMAL(15,2),
disbursed_at, disbursement_method, 
settled_at, refund_amount DECIMAL(15,2) (if any),
created_at, updated_at
```

### `per_diem_rates`
```
id, tenant_id, applies_to (city|position_level|both), 
city, position_level_id, rate_per_day DECIMAL(15,2), currency,
created_at, updated_at, deleted_at
```

### `travel_settlements`
```
id, tenant_id, travel_request_id, 
total_expense DECIMAL(15,2), advance_amount DECIMAL(15,2),
balance DECIMAL(15,2) (positive = company owes employee, negative = employee returns),
submitted_at, approved_at, paid_at, status (draft|submitted|approved|paid),
reimbursement_request_id (FK), created_at, updated_at
```

### `travel_itineraries`
```
id, tenant_id, travel_request_id, sequence INT,
date, from_location, to_location, transport_type,
booking_reference, cost DECIMAL(15,2), notes,
created_at, updated_at
```

---

## M27 — Loan / Cooperative (7 tables)

### `loan_policies`
```
id, tenant_id, applies_to_position_id (nullable), 
loan_type (cash_advance|short_term|long_term|cooperative),
max_amount_or_multiplier DECIMAL(10,2) (e.g., 3x gaji),
min_tenure_months_required INT, max_installments INT,
interest_rate_percent DECIMAL(5,2), interest_method (flat|effective),
processing_fee DECIMAL(15,2), 
created_at, updated_at, deleted_at
```

### `loans`
```
id, tenant_id, employee_id, loan_no, loan_type, 
principal DECIMAL(15,2), interest_rate DECIMAL(5,2),
tenor_months INT, installment_amount DECIMAL(15,2),
total_payable DECIMAL(15,2), 
purpose, attachment_path, 
approval_request_id, disbursement_run_id,
status (pending|approved|disbursed|active|completed|defaulted|rejected),
disbursed_at, completed_at, 
created_at, updated_at, deleted_at
```

### `loan_installments`
```
id, tenant_id, loan_id, installment_no INT,
due_date, payroll_run_id (nullable, FK saat deduct),
principal_amount, interest_amount, total_amount,
status (scheduled|paid|skipped|overdue), paid_at,
created_at, updated_at
```

### `cooperative_memberships`
```
id, tenant_id, employee_id, member_no, join_date,
status (active|inactive|terminated), terminated_at,
created_at, updated_at
```

### `cooperative_savings_types`
```
id, tenant_id, code (e.g., "pokok", "wajib", "sukarela"), 
name, is_mandatory BOOL, is_refundable_on_exit BOOL,
default_monthly_amount DECIMAL(15,2),
created_at, updated_at
```

### `cooperative_savings_transactions`
```
id, tenant_id, membership_id, savings_type_id,
transaction_type (deposit|withdraw|interest),
amount DECIMAL(15,2), payroll_run_id (nullable),
balance_after DECIMAL(15,2), notes,
created_at, updated_at
```

### `cooperative_shu_distributions`
```
id, tenant_id, year, total_shu DECIMAL(15,2),
distribution_rules JSON, status (calculated|approved|distributed),
distributed_at, created_at, updated_at
```

---

## M28 — Exit (5 tables)

### `exit_requests`
```
id, tenant_id, employee_id, exit_type (resignation|termination|retirement|end_of_contract|deceased),
notice_date DATE, last_working_date DATE,
reason TEXT, reason_category (better_offer|career_growth|salary|health|family|personal|other),
voluntary BOOL, eligible_for_rehire BOOL,
approval_request_id, status (pending|approved|in_clearance|completed|withdrawn),
exit_interview_id, severance_calculation_id, paklaring_letter_id,
created_at, updated_at, deleted_at
```

### `exit_clearance_items` (template + per request)
```
id, tenant_id, exit_request_id, item_name, 
assigned_to_role (it|hr|finance|ga|manager|other), 
assigned_to_employee_id, due_at,
status (pending|completed|waived), completed_at, completed_by_user_id, 
notes, attachment_path, created_at, updated_at
```

### `exit_clearance_templates`
```
id, tenant_id, name, items JSON, 
applies_to_exit_type, created_at, updated_at
```

### `exit_interviews`
```
id, tenant_id, exit_request_id, employee_id, 
interview_date, interviewer_employee_id,
form_responses JSON, summary TEXT, 
is_anonymous BOOL, would_recommend_company INT (1-10),
created_at, updated_at
```

### `severance_calculations`
```
id, tenant_id, exit_request_id, 
years_of_service DECIMAL(5,2), uang_pesangon DECIMAL(15,2),
uang_penghargaan DECIMAL(15,2), uang_penggantian_hak DECIMAL(15,2),
unused_leave_days DECIMAL(5,2), unused_leave_amount DECIMAL(15,2),
thr_proration DECIMAL(15,2), other_compensation DECIMAL(15,2),
total_severance DECIMAL(15,2), 
calculation_basis (uu_ck|company_policy|both), breakdown JSON,
created_at, updated_at
```

---

## M29 — Analytics (5 tables)

### `report_templates`
```
id, tenant_id, name, slug, description, 
query_definition JSON (filter, group, agg), 
output_format (table|chart|kpi_card|export),
visualization_config JSON, is_system BOOL, is_public BOOL,
created_by_user_id, created_at, updated_at, deleted_at
```

### `report_schedules`
```
id, tenant_id, report_template_id, name,
schedule_cron, recipients JSON (emails), format (pdf|excel|csv),
last_run_at, next_run_at, status (active|paused),
created_at, updated_at
```

### `report_executions`
```
id, tenant_id, report_template_id, schedule_id (nullable),
executed_by_user_id, parameters JSON, 
status (running|completed|failed), execution_time_ms INT,
output_path, error_message, created_at, completed_at
```

### `analytics_caches`
Pre-aggregated data untuk fast dashboard.
```
id, tenant_id, cache_key, dimension, period, 
data JSON, computed_at, expires_at, created_at
```

### `predictions` (Phase 5+)
```
id, tenant_id, model_name (turnover_risk|engagement_score),
subject_type (employee|department), subject_id, 
score DECIMAL(5,2), factors JSON, predicted_at, 
model_version, created_at
```

---

## Cross-Cutting Tables

### `users` (Filament + Sanctum)
```
id, tenant_id, employee_id (nullable for super-admin), 
name, email (unique per tenant), password,
two_factor_secret ENCRYPTED, two_factor_recovery_codes JSON ENCRYPTED,
two_factor_confirmed_at, 
last_login_at, last_login_ip, 
is_super_admin BOOL, is_active, 
created_at, updated_at, deleted_at
```

### `roles` & `permissions` (Spatie)
Standard Spatie tables: `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`.

### `activity_log` (Spatie Activitylog)
```
id, log_name, description, subject_type, subject_id, 
causer_type, causer_id, properties JSON (old + new), 
tenant_id, batch_uuid, created_at, updated_at
```

### `pii_access_logs`
```
id, tenant_id, user_id, accessed_entity_type, accessed_entity_id, 
accessed_field (e.g., "nik", "salary"), purpose, ip_address,
created_at
```

### `webhooks` (outbound subscriptions)
```
id, tenant_id, url, secret ENCRYPTED, event_types JSON,
is_active, last_triggered_at, total_failures INT,
created_at, updated_at, deleted_at
```

### `webhook_deliveries`
```
id, tenant_id, webhook_id, event_type, payload JSON,
status (pending|delivered|failed|retrying), 
response_code, response_body, attempts INT, 
next_retry_at, delivered_at, created_at, updated_at
```

### `integration_logs`
```
id, tenant_id, adapter_slug, direction (outbound|inbound),
request_payload JSON, response_payload JSON, 
status_code, duration_ms, error_message,
created_at
```

### `feature_flags`
```
id, tenant_id (nullable for global), feature_key, 
is_enabled, rollout_percent INT, 
conditions JSON, created_at, updated_at
```

### `tenant_settings`
```
id, tenant_id, key, value JSON, 
created_at, updated_at
UNIQUE(tenant_id, key)
```

### `data_export_requests` (UU PDP)
```
id, tenant_id, employee_id, request_type (access|portability|deletion),
status (pending|processing|completed|rejected),
requested_at, completed_at, file_path (for export),
created_at, updated_at
```

### `data_deletion_requests` (UU PDP)
```
id, tenant_id, employee_id, requested_at, 
scheduled_deletion_at (after 30d retention), executed_at, 
status, reason, created_at, updated_at
```

### `license_audit_log`
```
id, tenant_id, event_type (validated|expired|tampered|reissued),
license_key, fingerprint, ip_address, user_agent, 
event_data JSON, created_at
```

---

## Master Data Tables (Global, No Tenant)

- `countries`, `provinces`, `cities` (Indonesia + international)
- `holidays` (national + religious)
- `ptkp_rules` (per year)
- `ter_rates` (per year)
- `bpjs_rates` (per program per year)
- `bank_master` (list bank Indonesia + SWIFT)
- `klu_codes` (Klasifikasi Lapangan Usaha untuk NPWP)
- `umk_master` (UMK kabupaten/kota per tahun)

---

## Total Table Count

| Modul | Tables |
|---|---|
| Central / Tenant Mgmt | 5 |
| M01 Employee | 12 |
| M02 Organization | 9 |
| M03 Contract | 5 |
| M04 HR Letter | 4 |
| M05 Approval | 5 |
| M06 Announcement | 5 |
| M07 ESS | 1 |
| M08 E-Signature | 3 |
| M09 Shift | 5 |
| M10 Attendance | 6 |
| M11 Overtime | 3 |
| M12 Leave | 6 |
| M13 Holiday | 3 |
| M14 Payroll | 9 |
| M15 Tax | 6 |
| M16 BPJS | 7 |
| M17 Disbursement | 4 |
| M18 Reimbursement | 5 |
| M19 ATS | 10 |
| M20 Onboarding | 4 |
| M21 OKR | 5 |
| M22 Appraisal | 8 |
| M23 Competency | 6 |
| M24 LMS | 9 |
| M25 Asset | 5 |
| M26 Travel | 5 |
| M27 Loan | 7 |
| M28 Exit | 5 |
| M29 Analytics | 5 |
| Cross-cutting | 13 |
| Master Data | 8 |
| **TOTAL** | **~197** |

## Indexing Strategy

### Mandatory composite indexes (per tabel multi-tenant)
- `(tenant_id, deleted_at)` — soft delete filter
- `(tenant_id, created_at)` — recent queries
- `(tenant_id, status)` — status-based queries
- Primary FK columns also indexed

### Hot-path indexes
- `attendance_logs (tenant_id, employee_id, date)` — payroll fetch
- `payroll_items (tenant_id, payroll_run_id, employee_id)` — bulk query
- `leave_requests (tenant_id, employee_id, status)` — ESS dashboard
- `approval_requests (tenant_id, current_step_order, status)` — inbox

### Full-text search
- `employees (full_name, nickname, employee_no, employee_email)` — search box
- `candidates (full_name, email, current_position)` — ATS search
- `courses (title, description)` — LMS search

## Constraints & Data Integrity

- **CHECK constraints** untuk enum-like fields (status, type)
- **FK constraints** dengan `ON DELETE` strategy:
  - Soft entities (employees, etc.) — `ON DELETE RESTRICT`
  - Audit logs — `ON DELETE CASCADE` (cascade kalau parent dihapus permanent)
  - Pivot tables — `ON DELETE CASCADE`
- **UNIQUE constraints**:
  - `employees (tenant_id, employee_no)`
  - `employees.nik` (encrypted unique — via deterministic hash column)
  - `contracts (tenant_id, contract_no)`
  - `tenants.subdomain`
- **Triggers** minimal — prefer application-level logic untuk testability
- **Generated columns** untuk computed fields (e.g., `worked_minutes` di attendance)

## Soft Delete vs Hard Delete

- Default: **soft delete** semua master + transactional
- **Hard delete** hanya untuk:
  - PII data deletion request (UU PDP)
  - Cleanup expired temporary data (drafts >90 hari)
  - Test data di non-prod

## Archival Strategy

- Data >7 tahun: archive ke cold storage (S3 Glacier)
- Audit log: append-only, partition per bulan, retention 7 tahun
- Payroll history: keep in main DB minimal 3 tahun (untuk tax audit)
