<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // M14 Payroll
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // "2026-05"
            $table->enum('period_type', ['monthly', 'weekly', 'biweekly', 'daily'])->default('monthly');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('cutoff_attendance_date');
            $table->date('payroll_date');
            $table->enum('status', ['open', 'cutoff', 'processing', 'finalized', 'paid', 'locked'])->default('open');
            $table->timestamps();
            $table->index(['tenant_id', 'company_id', 'status']);
        });

        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_period_id')->constrained()->cascadeOnDelete();
            $table->enum('run_type', ['regular', 'off_cycle', 'bonus', 'thr', 'correction'])->default('regular');
            $table->enum('status', ['draft', 'calculating', 'review', 'approved', 'disbursed', 'finalized', 'failed'])->default('draft');
            $table->unsignedInteger('total_employees')->default(0);
            $table->decimal('total_gross', 15, 2)->default(0);
            $table->decimal('total_net', 15, 2)->default(0);
            $table->decimal('total_tax', 15, 2)->default(0);
            $table->decimal('total_bpjs_employer', 15, 2)->default(0);
            $table->decimal('total_bpjs_employee', 15, 2)->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('payroll_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code', 30);
            $table->string('name');
            $table->enum('type', ['earning', 'deduction', 'benefit']);
            $table->enum('calculation', ['fixed', 'formula', 'attendance_based', 'manual'])->default('fixed');
            $table->text('formula')->nullable();
            $table->decimal('default_amount', 15, 2)->default(0);
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_bpjs_basis')->default(false);
            $table->boolean('is_thr_basis')->default(false);
            $table->boolean('is_pension_basis')->default(false);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('gross', 15, 2)->default(0);
            $table->decimal('total_earnings', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('total_benefits', 15, 2)->default(0);
            $table->decimal('pph_21', 15, 2)->default(0);
            $table->decimal('bpjs_employer', 15, 2)->default(0);
            $table->decimal('bpjs_employee', 15, 2)->default(0);
            $table->decimal('net_pay', 15, 2)->default(0);
            $table->unsignedSmallInteger('working_days')->default(0);
            $table->unsignedSmallInteger('present_days')->default(0);
            $table->unsignedSmallInteger('absent_days')->default(0);
            $table->unsignedSmallInteger('leave_days')->default(0);
            $table->decimal('overtime_hours', 6, 2)->default(0);
            $table->decimal('overtime_amount', 15, 2)->default(0);
            $table->unsignedSmallInteger('prorate_days')->nullable();
            $table->decimal('prorate_factor', 6, 4)->default(1);
            $table->enum('status', ['pending', 'calculated', 'approved', 'paid', 'hold'])->default('pending');
            $table->text('hold_reason')->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();
            $table->unique(['payroll_run_id', 'employee_id']);
        });

        Schema::create('payroll_item_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_component_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('quantity', 8, 2)->nullable();
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->text('formula_used')->nullable();
            $table->json('calculation_breakdown')->nullable();
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_bpjs_basis')->default(false);
            $table->timestamps();
        });

        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('slip_number');
            $table->date('slip_date');
            $table->string('pdf_path')->nullable();
            $table->timestamp('sent_email_at')->nullable();
            $table->timestamp('sent_whatsapp_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->string('password_hint')->nullable();
            $table->timestamps();
        });

        // M15 Tax
        Schema::create('tax_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('npwp', 20);
            $table->string('klu_code', 10)->nullable();
            $table->enum('calculation_method', ['gross', 'nett', 'gross_up_thr'])->default('gross');
            $table->unsignedSmallInteger('ptkp_year');
            $table->unsignedSmallInteger('ter_year');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tax_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('period_year');
            $table->unsignedTinyInteger('period_month');
            $table->decimal('gross_basis', 15, 2);
            $table->decimal('ptkp_amount', 15, 2);
            $table->enum('ter_category', ['A', 'B', 'C'])->nullable();
            $table->decimal('ter_rate', 6, 4)->nullable();
            $table->decimal('pph_21_amount', 15, 2);
            $table->enum('method_used', ['ter', 'gross', 'nett', 'gross_up'])->default('ter');
            $table->json('calculation_breakdown')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'period_year', 'period_month']);
        });

        // M16 BPJS
        Schema::create('bpjs_company_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('npp_bpjs_kesehatan', 20)->nullable();
            $table->string('npp_bpjs_naker', 20)->nullable();
            $table->enum('jkk_risk_level', ['very_low', 'low', 'medium', 'high', 'very_high'])->default('low');
            $table->decimal('jkk_rate', 7, 5);
            $table->string('bpjs_branch_kesehatan')->nullable();
            $table->string('bpjs_branch_naker')->nullable();
            $table->timestamps();
        });

        Schema::create('bpjs_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('program', ['kesehatan', 'jht', 'jp', 'jkk', 'jkm']);
            $table->text('bpjs_number')->nullable();
            $table->date('enrolled_at');
            $table->date('terminated_at')->nullable();
            $table->enum('status', ['active', 'suspended', 'terminated'])->default('active');
            $table->timestamps();
            $table->unique(['employee_id', 'program']);
        });

        Schema::create('bpjs_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('program', ['kesehatan', 'jht', 'jp', 'jkk', 'jkm']);
            $table->decimal('basis_amount', 15, 2);
            $table->decimal('employer_amount', 15, 2);
            $table->decimal('employee_amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpjs_calculations');
        Schema::dropIfExists('bpjs_enrollments');
        Schema::dropIfExists('bpjs_company_profiles');
        Schema::dropIfExists('tax_calculations');
        Schema::dropIfExists('tax_settings');
        Schema::dropIfExists('payslips');
        Schema::dropIfExists('payroll_item_components');
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payroll_components');
        Schema::dropIfExists('payroll_runs');
        Schema::dropIfExists('payroll_periods');
    }
};
