<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // M19 ATS
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->longText('requirements')->nullable();
            $table->string('location')->nullable();
            $table->string('employment_type', 30)->nullable();
            $table->decimal('salary_min', 15, 2)->nullable();
            $table->decimal('salary_max', 15, 2)->nullable();
            $table->string('salary_currency', 3)->default('IDR');
            $table->enum('status', ['draft', 'published', 'closed', 'filled'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('posted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->json('external_links')->nullable();
            $table->string('public_token')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('location')->nullable();
            $table->string('current_position')->nullable();
            $table->unsignedTinyInteger('years_experience')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('photo_path')->nullable();
            $table->text('ai_summary')->nullable();
            $table->decimal('ai_score', 5, 2)->nullable();
            $table->json('talent_pool_tags')->nullable();
            $table->string('source', 50)->nullable();
            $table->foreignId('referred_by_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('application_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('order');
            $table->string('color', 7)->default('#6366f1');
            $table->boolean('is_terminal')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_posting_id')->constrained()->cascadeOnDelete();
            $table->foreignId('current_stage_id')->nullable()->constrained('application_stages')->nullOnDelete();
            $table->decimal('ai_match_score', 5, 2)->nullable();
            $table->text('cover_letter')->nullable();
            $table->decimal('salary_expectation', 15, 2)->nullable();
            $table->unsignedSmallInteger('notice_period_days')->nullable();
            $table->enum('status', ['active', 'hired', 'rejected', 'withdrawn', 'on_hold'])->default('active');
            $table->foreignId('hired_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('rejected_reason')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['candidate_id', 'job_posting_id']);
        });

        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->enum('interview_type', ['phone', 'video', 'onsite', 'technical', 'hr', 'panel'])->default('hr');
            $table->timestamp('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->json('interviewer_employee_ids')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'canceled', 'no_show'])->default('scheduled');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('interview_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interview_id')->constrained()->cascadeOnDelete();
            $table->foreignId('interviewer_employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedTinyInteger('overall_rating');
            $table->enum('recommendation', ['strong_yes', 'yes', 'maybe', 'no', 'strong_no']);
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('salary_offered', 15, 2);
            $table->text('other_benefits')->nullable();
            $table->string('offer_letter_path')->nullable();
            $table->date('start_date')->nullable();
            $table->enum('status', ['draft', 'sent', 'accepted', 'declined', 'withdrawn', 'expired'])->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });

        // M20 Onboarding
        Schema::create('onboarding_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('applies_to_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->foreignId('applies_to_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->unsignedSmallInteger('duration_days')->default(90);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('onboarding_template_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('onboarding_templates')->cascadeOnDelete();
            $table->unsignedSmallInteger('order');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('day_offset')->default(0);
            $table->enum('task_type', ['document_upload', 'training', 'meeting', 'signature', 'asset_assignment', 'custom'])->default('custom');
            $table->enum('assigned_role', ['new_hire', 'hr', 'manager', 'it', 'ga', 'buddy'])->default('new_hire');
            $table->boolean('is_mandatory')->default(true);
            $table->timestamps();
        });

        Schema::create('onboarding_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('onboarding_templates')->cascadeOnDelete();
            $table->foreignId('buddy_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date_planned')->nullable();
            $table->date('end_date_actual')->nullable();
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            $table->timestamps();
        });

        Schema::create('onboarding_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('process_id')->constrained('onboarding_processes')->cascadeOnDelete();
            $table->foreignId('template_task_id')->nullable()->constrained('onboarding_template_tasks')->nullOnDelete();
            $table->string('name');
            $table->timestamp('due_at')->nullable();
            $table->foreignId('assigned_to_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'skipped'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });

        Schema::create('probation_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('review_at_days');
            $table->date('review_date');
            $table->foreignId('reviewer_employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('rating', ['excellent', 'good', 'needs_improvement', 'unsatisfactory']);
            $table->text('feedback')->nullable();
            $table->enum('decision', ['extend_probation', 'confirm_permanent', 'terminate'])->default('confirm_permanent');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('probation_reviews');
        Schema::dropIfExists('onboarding_tasks');
        Schema::dropIfExists('onboarding_processes');
        Schema::dropIfExists('onboarding_template_tasks');
        Schema::dropIfExists('onboarding_templates');
        Schema::dropIfExists('offers');
        Schema::dropIfExists('interview_feedbacks');
        Schema::dropIfExists('interviews');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('application_stages');
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('job_postings');
    }
};
