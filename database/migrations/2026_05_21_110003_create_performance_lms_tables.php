<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // M21 OKR
        Schema::create('okr_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['planning', 'active', 'closed', 'archived'])->default('planning');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('objectives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cycle_id')->constrained('okr_cycles')->cascadeOnDelete();
            $table->foreignId('owner_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('parent_objective_id')->nullable()->references('id')->on('objectives')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('weight', 5, 2)->default(100);
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->boolean('is_public')->default(true);
            $table->enum('status', ['draft', 'active', 'completed', 'canceled'])->default('draft');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('key_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('objective_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('metric_type', ['number', 'percentage', 'currency', 'boolean'])->default('number');
            $table->decimal('start_value', 15, 2)->default(0);
            $table->decimal('target_value', 15, 2);
            $table->decimal('current_value', 15, 2)->default(0);
            $table->string('unit', 20)->nullable();
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('okr_check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('objective_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('key_result_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('check_in_date');
            $table->text('progress_update')->nullable();
            $table->unsignedTinyInteger('confidence_level')->nullable();
            $table->text('blockers')->nullable();
            $table->decimal('current_value', 15, 2)->nullable();
            $table->timestamps();
        });

        // M22 Appraisal
        Schema::create('appraisal_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('cycle_type', ['annual', 'semester', 'quarterly']);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('self_review_start')->nullable();
            $table->date('self_review_end')->nullable();
            $table->date('peer_review_start')->nullable();
            $table->date('peer_review_end')->nullable();
            $table->date('manager_review_start')->nullable();
            $table->date('manager_review_end')->nullable();
            $table->date('calibration_date')->nullable();
            $table->date('finalization_date')->nullable();
            $table->enum('status', ['planning', 'active', 'review', 'calibration', 'completed'])->default('planning');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('appraisal_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('applies_to_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('applies_to_level_id')->nullable()->constrained('job_levels')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('appraisal_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('form_id')->constrained('appraisal_forms')->cascadeOnDelete();
            $table->string('section', 100)->nullable();
            $table->unsignedSmallInteger('order');
            $table->enum('question_type', ['rating', 'text', 'multi_choice', 'competency_rating']);
            $table->text('question');
            $table->json('options')->nullable();
            $table->unsignedTinyInteger('scale_min')->default(1);
            $table->unsignedTinyInteger('scale_max')->default(5);
            $table->boolean('is_required')->default(true);
            $table->decimal('weight', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('appraisal_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cycle_id')->constrained('appraisal_cycles')->cascadeOnDelete();
            $table->foreignId('subject_employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('reviewer_employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('reviewer_type', ['self', 'peer', 'manager', 'subordinate', 'skip_level']);
            $table->foreignId('form_id')->constrained('appraisal_forms')->cascadeOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'submitted'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['cycle_id', 'subject_employee_id', 'reviewer_employee_id'], 'appr_unique_assign');
        });

        Schema::create('appraisal_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignment_id')->constrained('appraisal_assignments')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('appraisal_questions')->cascadeOnDelete();
            $table->text('answer_text')->nullable();
            $table->unsignedTinyInteger('answer_rating')->nullable();
            $table->string('answer_choice')->nullable();
            $table->timestamps();
        });

        Schema::create('appraisal_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cycle_id')->constrained('appraisal_cycles')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('self_score', 5, 2)->nullable();
            $table->decimal('peer_score', 5, 2)->nullable();
            $table->decimal('manager_score', 5, 2)->nullable();
            $table->decimal('subordinate_score', 5, 2)->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->string('final_rating')->nullable();
            $table->boolean('calibrated')->default(false);
            $table->timestamp('calibrated_at')->nullable();
            $table->foreignId('calibrated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['cycle_id', 'employee_id']);
        });

        Schema::create('nine_box_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cycle_id')->constrained('appraisal_cycles')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('performance_quadrant');
            $table->unsignedTinyInteger('potential_quadrant');
            $table->string('box_code', 3);
            $table->text('placement_notes')->nullable();
            $table->timestamps();
            $table->unique(['cycle_id', 'employee_id']);
        });

        // M23 Competency
        Schema::create('competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->boolean('is_core')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employee_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competency_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('current_level');
            $table->timestamp('assessed_at')->nullable();
            $table->foreignId('assessed_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
            $table->unique(['employee_id', 'competency_id']);
        });

        Schema::create('succession_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('critical_position_id')->constrained('positions')->cascadeOnDelete();
            $table->foreignId('successor_employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('readiness', ['ready_now', '1_2_years', '3_5_years', 'long_term'])->default('1_2_years');
            $table->enum('risk_if_vacant', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('notes')->nullable();
            $table->timestamp('last_reviewed_at')->nullable();
            $table->timestamps();
        });

        // M24 LMS
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->unsignedInteger('duration_minutes')->default(0);
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->string('thumbnail_path')->nullable();
            $table->foreignId('instructor_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->json('is_mandatory_for_roles')->nullable();
            $table->unsignedTinyInteger('passing_score')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('order');
            $table->string('title');
            $table->enum('content_type', ['video', 'pdf', 'scorm', 'link', 'text', 'quiz']);
            $table->string('content_url')->nullable();
            $table->string('content_path')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->timestamps();
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->timestamp('enrolled_at');
            $table->foreignId('enrolled_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->enum('status', ['enrolled', 'in_progress', 'completed', 'failed', 'expired'])->default('enrolled');
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'course_id']);
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('certificate_no');
            $table->timestamp('issued_at');
            $table->date('expires_at')->nullable();
            $table->string('certificate_path')->nullable();
            $table->foreignId('issued_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('succession_plans');
        Schema::dropIfExists('employee_competencies');
        Schema::dropIfExists('competencies');
        Schema::dropIfExists('nine_box_placements');
        Schema::dropIfExists('appraisal_ratings');
        Schema::dropIfExists('appraisal_responses');
        Schema::dropIfExists('appraisal_assignments');
        Schema::dropIfExists('appraisal_questions');
        Schema::dropIfExists('appraisal_forms');
        Schema::dropIfExists('appraisal_cycles');
        Schema::dropIfExists('okr_check_ins');
        Schema::dropIfExists('key_results');
        Schema::dropIfExists('objectives');
        Schema::dropIfExists('okr_cycles');
    }
};
