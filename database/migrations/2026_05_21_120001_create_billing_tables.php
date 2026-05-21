<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedInteger('max_employees')->nullable();
            $table->decimal('price_monthly', 15, 2)->default(0);
            $table->decimal('price_annual', 15, 2)->default(0);
            $table->string('currency', 3)->default('IDR');
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['trial', 'active', 'past_due', 'canceled', 'expired', 'suspended'])->default('trial');
            $table->date('current_period_start');
            $table->date('current_period_end');
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->enum('billing_cycle', ['monthly', 'annual'])->default('monthly');
            $table->unsignedInteger('seats')->default(1);
            $table->string('external_subscription_id')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('number')->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->date('issue_date');
            $table->date('due_date');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->string('currency', 3)->default('IDR');
            $table->enum('status', ['draft', 'open', 'paid', 'past_due', 'void', 'refunded'])->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->string('paid_via')->nullable();
            $table->string('pdf_path')->nullable();
            $table->json('line_items')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('adapter_config_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->unique(); // internal reference
            $table->string('external_reference_id')->nullable(); // payment gateway ID
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IDR');
            $table->string('payment_method'); // qris, va, ewallet, cc, paypal, manual, etc.
            $table->string('payment_channel')->nullable(); // bca, mandiri, dana, ovo, etc.
            $table->enum('status', ['pending', 'paid', 'failed', 'expired', 'refunded'])->default('pending');
            $table->string('payment_url')->nullable(); // checkout URL
            $table->text('qr_string')->nullable();
            $table->string('va_number')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->json('webhook_payload')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'status']);
            $table->index('external_reference_id');
        });

        Schema::create('payment_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('adapter_slug');
            $table->string('event_type')->nullable();
            $table->json('headers')->nullable();
            $table->json('raw_payload');
            $table->string('signature')->nullable();
            $table->boolean('signature_valid')->default(false);
            $table->boolean('processed')->default(false);
            $table->string('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_logs');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
    }
};
