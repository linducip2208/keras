<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // M25 Asset
        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('depreciation_method', ['straight_line', 'none'])->default('straight_line');
            $table->unsignedTinyInteger('useful_life_years')->nullable();
            $table->string('gl_account_code')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('asset_categories')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('color', 30)->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_amount', 15, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->date('warranty_until')->nullable();
            $table->decimal('current_value', 15, 2)->nullable();
            $table->enum('condition', ['new', 'good', 'fair', 'damaged', 'written_off'])->default('good');
            $table->string('location')->nullable();
            $table->enum('status', ['in_stock', 'assigned', 'maintenance', 'disposed', 'lost'])->default('in_stock');
            $table->string('photo_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->timestamp('assigned_at');
            $table->foreignId('assigned_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('returned_at')->nullable();
            $table->foreignId('returned_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('condition_at_assign', 50)->nullable();
            $table->string('condition_at_return', 50)->nullable();
            $table->string('handover_form_path')->nullable();
            $table->string('return_form_path')->nullable();
            $table->enum('status', ['active', 'returned', 'lost', 'damaged'])->default('active');
            $table->timestamps();
        });

        // M26 Travel/SPPD
        Schema::create('travel_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('request_no');
            $table->string('origin');
            $table->string('destination');
            $table->text('purpose');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('transport_mode', ['flight', 'train', 'car', 'bus', 'other'])->default('flight');
            $table->decimal('estimated_cost', 15, 2)->nullable();
            $table->foreignId('sppd_letter_id')->nullable()->constrained('hr_letters')->nullOnDelete();
            $table->enum('status', ['draft', 'approved', 'in_progress', 'completed', 'canceled'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('per_diem_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->enum('applies_to', ['city', 'position_level', 'both'])->default('city');
            $table->string('city')->nullable();
            $table->foreignId('position_level_id')->nullable()->constrained('job_levels')->nullOnDelete();
            $table->decimal('rate_per_day', 15, 2);
            $table->string('currency', 3)->default('IDR');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('travel_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('travel_request_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_expense', 15, 2)->default(0);
            $table->decimal('advance_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'paid'])->default('draft');
            $table->foreignId('reimbursement_request_id')->nullable()->constrained('reimbursement_requests')->nullOnDelete();
            $table->timestamps();
        });

        // M27 Loan
        Schema::create('loan_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('applies_to_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->enum('loan_type', ['cash_advance', 'short_term', 'long_term', 'cooperative']);
            $table->decimal('max_amount_or_multiplier', 10, 2);
            $table->unsignedSmallInteger('min_tenure_months_required')->default(6);
            $table->unsignedSmallInteger('max_installments')->default(12);
            $table->decimal('interest_rate_percent', 5, 2)->default(0);
            $table->enum('interest_method', ['flat', 'effective'])->default('flat');
            $table->decimal('processing_fee', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('loan_no');
            $table->enum('loan_type', ['cash_advance', 'short_term', 'long_term', 'cooperative']);
            $table->decimal('principal', 15, 2);
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->unsignedSmallInteger('tenor_months')->default(1);
            $table->decimal('installment_amount', 15, 2);
            $table->decimal('total_payable', 15, 2);
            $table->text('purpose')->nullable();
            $table->string('attachment_path')->nullable();
            $table->foreignId('disbursement_run_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'disbursed', 'active', 'completed', 'defaulted', 'rejected'])->default('pending');
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('loan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('installment_no');
            $table->date('due_date');
            $table->foreignId('payroll_run_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('principal_amount', 15, 2);
            $table->decimal('interest_amount', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->enum('status', ['scheduled', 'paid', 'skipped', 'overdue'])->default('scheduled');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        // M28 Exit
        Schema::create('exit_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('exit_type', ['resignation', 'termination', 'retirement', 'end_of_contract', 'deceased']);
            $table->date('notice_date');
            $table->date('last_working_date');
            $table->text('reason')->nullable();
            $table->string('reason_category', 50)->nullable();
            $table->boolean('voluntary')->default(true);
            $table->boolean('eligible_for_rehire')->default(true);
            $table->enum('status', ['pending', 'approved', 'in_clearance', 'completed', 'withdrawn'])->default('pending');
            $table->foreignId('paklaring_letter_id')->nullable()->constrained('hr_letters')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('exit_clearance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exit_request_id')->constrained()->cascadeOnDelete();
            $table->string('item_name');
            $table->enum('assigned_to_role', ['it', 'hr', 'finance', 'ga', 'manager', 'other'])->default('hr');
            $table->foreignId('assigned_to_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('due_at')->nullable();
            $table->enum('status', ['pending', 'completed', 'waived'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });

        Schema::create('exit_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exit_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('interview_date');
            $table->foreignId('interviewer_employee_id')->constrained('employees')->cascadeOnDelete();
            $table->json('form_responses')->nullable();
            $table->text('summary')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->unsignedTinyInteger('would_recommend_company')->nullable();
            $table->timestamps();
        });

        Schema::create('severance_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exit_request_id')->constrained()->cascadeOnDelete();
            $table->decimal('years_of_service', 5, 2);
            $table->decimal('uang_pesangon', 15, 2)->default(0);
            $table->decimal('uang_penghargaan', 15, 2)->default(0);
            $table->decimal('uang_penggantian_hak', 15, 2)->default(0);
            $table->decimal('unused_leave_days', 5, 2)->default(0);
            $table->decimal('unused_leave_amount', 15, 2)->default(0);
            $table->decimal('thr_proration', 15, 2)->default(0);
            $table->decimal('other_compensation', 15, 2)->default(0);
            $table->decimal('total_severance', 15, 2)->default(0);
            $table->enum('calculation_basis', ['uu_ck', 'company_policy', 'both'])->default('uu_ck');
            $table->json('breakdown')->nullable();
            $table->timestamps();
        });

        // M29 Analytics
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->json('query_definition');
            $table->enum('output_format', ['table', 'chart', 'kpi_card', 'export'])->default('table');
            $table->json('visualization_config')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_public')->default(false);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('report_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('report_template_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('schedule_cron');
            $table->json('recipients');
            $table->enum('format', ['pdf', 'excel', 'csv'])->default('pdf');
            $table->timestamp('last_run_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->enum('status', ['active', 'paused'])->default('active');
            $table->timestamps();
        });

        // CX01 user_devices (FCM tokens, ESS mobile)
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('device_type', ['ios', 'android', 'web']);
            $table->text('device_token');
            $table->string('device_id')->nullable();
            $table->string('app_version', 20)->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
        });

        // CX08 adapter_configs (dynamic integrations per CLAUDE.md §2)
        Schema::create('adapter_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('domain', 50); // bank, esign, whatsapp, llm, ocr, mail, sms, storage, accounting
            $table->string('name'); // user input
            $table->string('format', 50);
            $table->string('base_url')->nullable();
            $table->text('api_key_encrypted')->nullable();
            $table->text('api_secret_encrypted')->nullable();
            $table->json('extra_headers')->nullable();
            $table->json('extra_config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_test_mode')->default(false);
            $table->timestamp('last_tested_at')->nullable();
            $table->string('last_test_status', 20)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'domain']);
        });

        // CX07 webhooks outbound
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->text('secret_encrypted')->nullable();
            $table->json('event_types');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->unsignedInteger('total_failures')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // CX09 data export/deletion (UU PDP compliance)
        Schema::create('data_export_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('request_type', ['access', 'portability', 'deletion']);
            $table->enum('status', ['pending', 'processing', 'completed', 'rejected'])->default('pending');
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_export_requests');
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('adapter_configs');
        Schema::dropIfExists('user_devices');
        Schema::dropIfExists('report_schedules');
        Schema::dropIfExists('report_templates');
        Schema::dropIfExists('severance_calculations');
        Schema::dropIfExists('exit_interviews');
        Schema::dropIfExists('exit_clearance_items');
        Schema::dropIfExists('exit_requests');
        Schema::dropIfExists('loan_installments');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('loan_policies');
        Schema::dropIfExists('travel_settlements');
        Schema::dropIfExists('per_diem_rates');
        Schema::dropIfExists('travel_requests');
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_categories');
    }
};
