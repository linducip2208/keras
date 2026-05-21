<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // M04 HR Letter
        Schema::create('hr_letter_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->enum('letter_type', ['sk', 'paklaring', 'sp1', 'sp2', 'sp3', 'spd', 'mutasi', 'promosi', 'demosi', 'referensi', 'custom']);
            $table->string('name');
            $table->longText('body_template');
            $table->json('variables')->nullable();
            $table->string('default_signer_role')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('hr_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('letter_type', ['sk', 'paklaring', 'sp1', 'sp2', 'sp3', 'spd', 'mutasi', 'promosi', 'demosi', 'referensi', 'custom']);
            $table->foreignId('template_id')->nullable()->constrained('hr_letter_templates')->nullOnDelete();
            $table->string('letter_no');
            $table->date('letter_date');
            $table->json('variables_data')->nullable();
            $table->longText('body_rendered')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['draft', 'finalized', 'signed', 'delivered', 'archived'])->default('draft');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->string('e_sign_provider')->nullable();
            $table->string('e_sign_reference_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'letter_no']);
        });

        Schema::create('hr_letter_number_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('letter_type', 30);
            $table->unsignedSmallInteger('year');
            $table->string('sequence_format')->default('{TYPE}/{YEAR}/{MONTH}/{SEQ:4}');
            $table->unsignedInteger('last_sequence')->default(0);
            $table->timestamps();
            $table->unique(['tenant_id', 'letter_type', 'year']);
        });

        // M06 Announcement
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('body');
            $table->json('attachment_paths')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('segment')->nullable();
            $table->timestamp('publish_at')->nullable();
            $table->timestamp('expire_at')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->unsignedSmallInteger('priority')->default(5);
            $table->enum('status', ['draft', 'published', 'expired'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('announcement_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('announcement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
            $table->unique(['announcement_id', 'employee_id']);
        });

        // M17 Disbursement
        Schema::create('disbursement_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_run_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('disbursement_method', ['batch_file', 'instant', 'manual']);
            $table->string('adapter_slug')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->unsignedInteger('total_count')->default(0);
            $table->enum('status', ['draft', 'processing', 'completed', 'partially_completed', 'failed'])->default('draft');
            $table->string('file_path')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('disbursement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('disbursement_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained('employee_bank_accounts')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('bank_code', 10);
            $table->string('bank_name');
            $table->text('account_number');
            $table->string('account_holder');
            $table->enum('status', ['pending', 'sent', 'completed', 'failed', 'reversed'])->default('pending');
            $table->string('external_reference_id')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        // M18 Reimbursement
        Schema::create('reimbursement_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->decimal('max_per_request', 15, 2)->nullable();
            $table->boolean('requires_receipt')->default(true);
            $table->boolean('requires_approval')->default(true);
            $table->string('gl_account_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reimbursement_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('request_no');
            $table->decimal('total_amount', 15, 2);
            $table->string('currency', 3)->default('IDR');
            $table->text('purpose')->nullable();
            $table->foreignId('business_trip_id')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'paid', 'canceled'])->default('draft');
            $table->enum('disbursement_method', ['payroll', 'separate'])->default('payroll');
            $table->foreignId('disbursement_run_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reimbursement_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('request_id')->constrained('reimbursement_requests')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('reimbursement_categories')->cascadeOnDelete();
            $table->date('item_date');
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->string('receipt_path')->nullable();
            $table->json('ocr_data')->nullable();
            $table->string('vendor_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reimbursement_items');
        Schema::dropIfExists('reimbursement_requests');
        Schema::dropIfExists('reimbursement_categories');
        Schema::dropIfExists('disbursement_items');
        Schema::dropIfExists('disbursement_runs');
        Schema::dropIfExists('announcement_recipients');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('hr_letter_number_counters');
        Schema::dropIfExists('hr_letters');
        Schema::dropIfExists('hr_letter_templates');
    }
};
