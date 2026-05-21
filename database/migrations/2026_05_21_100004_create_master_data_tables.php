<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Holidays (Indonesia + custom per tenant via override table)
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2)->default('ID');
            $table->unsignedSmallInteger('year');
            $table->date('date');
            $table->string('name');
            $table->enum('type', ['national', 'religious', 'cuti_bersama']);
            $table->enum('religion', ['islam', 'christian', 'catholic', 'hindu', 'buddha', 'confucian'])->nullable();
            $table->timestamps();
            $table->index(['country_code', 'year']);
            $table->index('date');
        });

        // PTKP Rules — Penghasilan Tidak Kena Pajak per status
        Schema::create('ptkp_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('status', 10); // TK_0, TK_1, ..., K_3
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
            $table->timestamps();
            $table->unique(['year', 'status']);
        });

        // TER Rates (Tarif Efektif Rata-rata PMK 168/2023)
        Schema::create('ter_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->enum('category', ['A', 'B', 'C']); // A: TK/0..K/1, B: TK/2..K/2, C: K/3
            $table->decimal('bracket_from', 15, 2);
            $table->decimal('bracket_to', 15, 2)->nullable(); // null = no upper bound
            $table->decimal('rate_percent', 6, 4); // 0.0000 - 100.0000
            $table->timestamps();
            $table->index(['year', 'category']);
        });

        // BPJS Rates (Kesehatan + Ketenagakerjaan)
        Schema::create('bpjs_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->enum('program', ['kesehatan', 'jht', 'jp', 'jkk', 'jkm']);
            $table->decimal('employer_rate', 7, 5);
            $table->decimal('employee_rate', 7, 5);
            $table->decimal('salary_cap', 15, 2)->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->index(['year', 'program']);
        });

        // Bank Master (BCA, Mandiri, dll.)
        Schema::create('bank_master', function (Blueprint $table) {
            $table->id();
            $table->string('code'); // bank code BI
            $table->string('name');
            $table->string('swift', 11)->nullable();
            $table->boolean('supports_batch_file')->default(false);
            $table->string('batch_format')->nullable(); // bca, mandiri, bni, bri, cimb
            $table->boolean('supports_instant_api')->default(false);
            $table->timestamps();
        });

        // KLU Codes (Klasifikasi Lapangan Usaha untuk NPWP)
        Schema::create('klu_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->string('category')->nullable();
            $table->timestamps();
        });

        // UMK (Upah Minimum Kabupaten/Kota) master per tahun
        Schema::create('umk_master', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->string('province');
            $table->string('city');
            $table->decimal('amount', 15, 2);
            $table->timestamps();
            $table->index(['year', 'province']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umk_master');
        Schema::dropIfExists('klu_codes');
        Schema::dropIfExists('bank_master');
        Schema::dropIfExists('bpjs_rates');
        Schema::dropIfExists('ter_rates');
        Schema::dropIfExists('ptkp_rules');
        Schema::dropIfExists('holidays');
    }
};
