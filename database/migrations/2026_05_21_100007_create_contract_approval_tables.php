<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // M03 Contract
        Schema::create('contract_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('contract_type', ['PKWT', 'PKWTT', 'probation', 'intern', 'outsource', 'freelance', 'daily']);
            $table->longText('body_template');
            $table->json('variables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('contract_type', ['PKWT', 'PKWTT', 'probation', 'intern', 'outsource', 'freelance', 'daily']);
            $table->string('contract_no');
            $table->foreignId('template_id')->nullable()->constrained('contract_templates')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->decimal('salary_base', 15, 2);
            $table->string('salary_currency', 3)->default('IDR');
            $table->foreignId('job_title_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('working_hours_per_week')->default(40);
            $table->enum('status', ['draft', 'active', 'expired', 'terminated', 'renewed'])->default('draft');
            $table->foreignId('parent_contract_id')->nullable()->references('id')->on('contracts')->nullOnDelete();
            $table->string('file_path')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('signed_by_employee_at')->nullable();
            $table->timestamp('signed_by_company_at')->nullable();
            $table->string('e_sign_provider')->nullable();
            $table->string('e_sign_reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'contract_no']);
            $table->index(['tenant_id', 'employee_id', 'status']);
        });

        // M05 Approval Workflow
        Schema::create('approval_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->enum('applies_to', ['leave', 'overtime', 'reimburse', 'travel', 'loan', 'attendance', 'custom']);
            $table->json('conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('priority')->default(10);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('approval_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_id')->constrained('approval_workflows')->cascadeOnDelete();
            $table->unsignedSmallInteger('order');
            $table->string('name');
            $table->enum('approver_type', ['employee', 'role', 'position', 'manager', 'manager_of_manager', 'hr', 'finance', 'custom']);
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->enum('approval_mode', ['single', 'all', 'any'])->default('single');
            $table->unsignedInteger('timeout_hours')->nullable();
            $table->foreignId('escalation_step_id')->nullable()->references('id')->on('approval_workflow_steps')->nullOnDelete();
            $table->boolean('is_optional')->default(false);
            $table->timestamps();
        });

        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_id')->constrained('approval_workflows')->cascadeOnDelete();
            $table->string('requestable_type');
            $table->unsignedBigInteger('requestable_id');
            $table->foreignId('requester_employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'canceled', 'escalated'])->default('pending');
            $table->unsignedSmallInteger('current_step_order')->default(1);
            $table->unsignedSmallInteger('total_steps')->default(1);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['requestable_type', 'requestable_id']);
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('approval_request_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approval_request_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('step_order');
            $table->foreignId('approver_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->enum('action', ['pending', 'approved', 'rejected', 'delegated', 'escalated'])->default('pending');
            $table->text('comment')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->foreignId('delegated_to_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });

        // Document categories
        Schema::create('document_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->boolean('requires_expiry')->default(false);
            $table->unsignedSmallInteger('alert_days_before')->default(30);
            $table->boolean('is_mandatory')->default(false);
            $table->enum('applies_to', ['employee', 'company', 'both'])->default('employee');
            $table->timestamps();
        });

        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('document_categories')->nullOnDelete();
            $table->string('name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->date('expires_at')->nullable();
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('document_categories');
        Schema::dropIfExists('approval_request_steps');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('approval_workflow_steps');
        Schema::dropIfExists('approval_workflows');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('contract_templates');
    }
};
