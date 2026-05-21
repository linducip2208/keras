<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // M09 Shift
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('name');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('break_minutes')->default(60);
            $table->decimal('working_hours', 4, 2)->default(8);
            $table->boolean('crosses_midnight')->default(false);
            $table->unsignedSmallInteger('allowed_late_minutes')->default(0);
            $table->unsignedSmallInteger('allowed_early_leave_minutes')->default(0);
            $table->string('color', 7)->default('#3b82f6');
            $table->boolean('is_off_day')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index('tenant_id');
        });

        Schema::create('shift_patterns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('days_in_cycle');
            $table->json('shift_sequence'); // [{day:1, shift_id:1}, ...]
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_off_day')->default(false);
            $table->enum('source', ['template', 'manual', 'swap'])->default('template');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'date']);
            $table->index(['tenant_id', 'date']);
        });

        // M10 Attendance
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamp('check_in_at')->nullable();
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->text('check_in_address')->nullable();
            $table->string('check_in_photo_path')->nullable();
            $table->enum('check_in_method', ['gps', 'fingerprint', 'qr', 'face', 'manual'])->nullable();
            $table->foreignId('check_in_branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->boolean('check_in_is_late')->default(false);
            $table->unsignedInteger('check_in_late_minutes')->default(0);
            $table->boolean('check_in_is_mock_gps')->default(false);

            $table->timestamp('check_out_at')->nullable();
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();
            $table->text('check_out_address')->nullable();
            $table->string('check_out_photo_path')->nullable();
            $table->enum('check_out_method', ['gps', 'fingerprint', 'qr', 'face', 'manual'])->nullable();
            $table->foreignId('check_out_branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->boolean('check_out_is_early')->default(false);
            $table->unsignedInteger('check_out_early_minutes')->default(0);
            $table->boolean('check_out_is_mock_gps')->default(false);

            $table->unsignedInteger('worked_minutes')->default(0);
            $table->unsignedInteger('overtime_minutes')->default(0);
            $table->enum('status', ['present', 'absent', 'leave', 'half_day', 'holiday', 'weekend'])->default('absent');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'date']);
            $table->index(['tenant_id', 'date']);
            $table->index(['tenant_id', 'employee_id', 'date']);
        });

        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attendance_log_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->timestamp('requested_check_in_at')->nullable();
            $table->timestamp('requested_check_out_at')->nullable();
            $table->text('reason');
            $table->string('attachment_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();
        });

        Schema::create('geofence_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->unsignedInteger('radius_meters')->default(100);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->timestamps();
        });

        // M11 Overtime
        Schema::create('overtime_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('planned_start')->nullable();
            $table->time('planned_end')->nullable();
            $table->timestamp('actual_start')->nullable();
            $table->timestamp('actual_end')->nullable();
            $table->decimal('planned_hours', 4, 2)->default(0);
            $table->decimal('actual_hours', 4, 2)->default(0);
            $table->text('reason')->nullable();
            $table->string('attachment_path')->nullable();
            $table->enum('overtime_type', ['workday', 'weekend', 'holiday'])->default('workday');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->decimal('amount_calculated', 15, 2)->default(0);
            $table->boolean('use_compensatory_leave')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'employee_id', 'date']);
        });

        // M12 Leave
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code', 20);
            $table->string('name');
            $table->decimal('days_per_year', 5, 2)->nullable();
            $table->boolean('is_paid')->default(true);
            $table->boolean('requires_attachment')->default(false);
            $table->boolean('requires_balance')->default(true);
            $table->enum('gender_specific', ['M', 'F'])->nullable();
            $table->unsignedSmallInteger('max_consecutive_days')->nullable();
            $table->unsignedSmallInteger('min_notice_days')->default(0);
            $table->boolean('can_be_half_day')->default(true);
            $table->boolean('can_carry_over')->default(false);
            $table->decimal('carry_over_max_days', 5, 2)->nullable();
            $table->unsignedSmallInteger('carry_over_expires_in_months')->nullable();
            $table->json('deducts_from')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year');
            $table->decimal('allocated', 5, 2)->default(0);
            $table->decimal('used', 5, 2)->default(0);
            $table->decimal('carried_over', 5, 2)->default(0);
            $table->decimal('pending', 5, 2)->default(0);
            $table->date('expires_at')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'leave_type_id', 'year']);
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days_count', 5, 2);
            $table->boolean('is_half_day')->default(false);
            $table->enum('half_day_period', ['am', 'pm'])->nullable();
            $table->text('reason')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('contact_during_leave')->nullable();
            $table->foreignId('delegate_employee_id')->nullable()->references('id')->on('employees')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'canceled'])->default('pending');
            $table->timestamp('applied_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('leave_balances');
        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('overtime_requests');
        Schema::dropIfExists('geofence_zones');
        Schema::dropIfExists('attendance_corrections');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('employee_schedules');
        Schema::dropIfExists('shift_patterns');
        Schema::dropIfExists('shifts');
    }
};
