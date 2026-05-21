<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('whatsapp_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('phone_number')->index(); // E.164 format
            $table->foreignId('employee_id')->nullable()->constrained()->nullOnDelete();
            $table->string('state')->default('idle'); // idle, awaiting_otp, awaiting_location, awaiting_selfie, awaiting_leave_dates, awaiting_leave_reason
            $table->json('state_data')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('session_id')->nullable()->constrained('whatsapp_sessions')->nullOnDelete();
            $table->string('phone_number')->index();
            $table->enum('direction', ['inbound', 'outbound']);
            $table->string('message_type', 30)->default('text'); // text, image, location, document, template
            $table->text('body')->nullable();
            $table->json('media')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
        Schema::dropIfExists('whatsapp_sessions');
    }
};
