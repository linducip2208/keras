<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('job_title_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('job_level_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_no');
            $table->text('nik')->nullable(); // encrypted
            $table->text('npwp')->nullable(); // encrypted
            $table->string('full_name');
            $table->string('nickname')->nullable();
            $table->enum('gender', ['M', 'F']);
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('religion', ['islam', 'christian', 'catholic', 'hindu', 'buddha', 'confucian', 'other'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->default('single');
            $table->string('ptkp_status', 10)->default('TK_0');
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable();
            $table->string('nationality', 3)->default('IDN');
            $table->text('ktp_address')->nullable();
            $table->text('current_address')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_alt')->nullable();
            $table->string('personal_email')->nullable();
            $table->string('employee_email')->nullable();
            $table->date('join_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->enum('employment_status', ['active', 'probation', 'on_leave', 'suspended', 'resigned', 'terminated'])->default('probation');
            $table->enum('employment_type', ['permanent', 'contract', 'probation', 'intern', 'outsource', 'freelance'])->default('probation');
            $table->date('last_working_date')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->string('qr_badge_code')->nullable()->unique();
            $table->foreignId('manager_employee_id')->nullable()->references('id')->on('employees')->nullOnDelete();
            $table->foreignId('dotted_line_manager_id')->nullable()->references('id')->on('employees')->nullOnDelete();
            $table->json('custom_fields')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'employee_no']);
            $table->index(['tenant_id', 'employment_status']);
            $table->index(['tenant_id', 'join_date']);
            $table->index(['tenant_id', 'company_id', 'department_id']);
        });

        Schema::create('employee_families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('relation', ['spouse', 'child', 'parent', 'sibling']);
            $table->string('name');
            $table->enum('gender', ['M', 'F'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->text('nik')->nullable();
            $table->boolean('is_bpjs_dependent')->default(false);
            $table->boolean('is_ptkp_dependent')->default(false);
            $table->timestamps();
        });

        Schema::create('employee_emergency_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('relation');
            $table->string('phone');
            $table->text('address')->nullable();
            $table->unsignedInteger('priority')->default(1);
            $table->timestamps();
        });

        Schema::create('employee_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->enum('level', ['SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3']);
            $table->string('school_name');
            $table->string('major')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->unsignedSmallInteger('start_year')->nullable();
            $table->unsignedSmallInteger('end_year')->nullable();
            $table->boolean('is_highest')->default(false);
            $table->timestamps();
        });

        Schema::create('employee_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('bank_name');
            $table->text('account_number'); // encrypted
            $table->string('account_holder_name');
            $table->boolean('is_primary')->default(true);
            $table->decimal('allocation_percentage', 5, 2)->default(100);
            $table->timestamps();
        });

        Schema::create('employee_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('job_title_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('job_level_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('manager_employee_id')->nullable()->references('id')->on('employees')->nullOnDelete();
            $table->date('effective_since')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_assignments');
        Schema::dropIfExists('employee_bank_accounts');
        Schema::dropIfExists('employee_educations');
        Schema::dropIfExists('employee_emergency_contacts');
        Schema::dropIfExists('employee_families');
        Schema::dropIfExists('employees');
    }
};
