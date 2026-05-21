<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_screenings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->foreignId('adapter_config_id')->nullable()->constrained()->nullOnDelete();
            $table->string('adapter_name'); // snapshot name in case adapter deleted
            $table->string('model_used')->nullable(); // e.g., "gpt-4o-mini", "deepseek-chat", "llama-3.3-70b"
            $table->decimal('score', 5, 2)->nullable(); // 0-100
            $table->text('summary')->nullable();
            $table->json('strengths')->nullable();
            $table->json('weaknesses')->nullable();
            $table->string('recommendation')->nullable(); // strong_yes, yes, maybe, no, strong_no
            $table->text('reasoning')->nullable();
            $table->json('raw_response')->nullable();
            $table->json('usage')->nullable(); // tokens consumed
            $table->decimal('cost', 10, 4)->nullable();
            $table->string('status', 20)->default('completed'); // running, completed, failed
            $table->string('error_message')->nullable();
            $table->foreignId('triggered_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['application_id', 'created_at']);
            $table->index(['tenant_id', 'adapter_config_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_screenings');
    }
};
