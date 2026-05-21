<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('subdomain')->unique();
            $table->string('custom_domain')->nullable()->unique();
            $table->enum('plan', ['starter', 'basic', 'essential', 'suite', 'self_host'])->default('starter');
            $table->enum('status', ['active', 'suspended', 'trial', 'expired'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->string('license_key')->nullable();
            $table->string('country', 2)->default('ID');
            $table->string('timezone')->default('Asia/Jakarta');
            $table->string('locale', 5)->default('id');
            $table->string('currency', 3)->default('IDR');
            $table->string('billing_email')->nullable();
            $table->foreignId('owner_user_id')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->boolean('is_super_admin')->default(false)->after('remember_token');
            $table->boolean('is_active')->default(true)->after('is_super_admin');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->softDeletes();
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn(['tenant_id', 'is_super_admin', 'is_active', 'last_login_at', 'last_login_ip']);
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('tenants');
    }
};
