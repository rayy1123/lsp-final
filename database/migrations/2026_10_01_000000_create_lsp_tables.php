<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: CreateLspTables
 * UNTUK APA: Membuat tabel certification_schemes, participants, dan lsp_report_settings.
 */
return new class extends Migration
{
    /**
     * FUNGSI: up()
     * UNTUK APA: Menjalankan proses migrasi struktur tabel database ke MySQL.
     * ALUR KERJA:
     * 1. Membuat tabel 'certification_schemes' (Master skema).
     * 2. Membuat tabel 'participants' (Peserta asesi) dengan constraint foreign key ke skema (ON DELETE CASCADE).
     * 3. Membuat tabel 'lsp_report_settings' (Konfigurasi CRUD cetak isi laporan/berita acara).
     */
    public function up(): void
    {
        // 1. Master Skema Sertifikasi
        Schema::create('certification_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('scheme_code')->unique();
            $table->string('scheme_name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Data Peserta / Asesi
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheme_id')->constrained('certification_schemes')->cascadeOnDelete();
            $table->string('registration_number')->unique();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone_number');
            $table->text('address')->nullable();
            $table->string('status')->default('Belum Kompeten');
            $table->timestamps();
        });

        // 3. Konfigurasi Konten Cetak Laporan / Berita Acara USK (CRUD)
        Schema::create('lsp_report_settings', function (Blueprint $table) {
            $table->id();
            $table->string('template_name')->default('Standar LSP');
            $table->string('institution_name');
            $table->string('tuk_name');
            $table->text('address');
            $table->text('contact_info');
            $table->string('document_title');
            $table->string('document_number');
            $table->string('tuk_location');
            $table->date('assessment_date')->nullable();
            $table->string('assessment_method');
            $table->text('closing_statement');
            $table->string('signer1_role');
            $table->string('signer1_name');
            $table->string('signer1_reg_number')->nullable();
            $table->string('signer2_city');
            $table->string('signer2_role');
            $table->string('signer2_name');
            $table->string('signer2_reg_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * FUNGSI: down()
     * UNTUK APA: Membatalkan/rollback seluruh tabel yang dibuat saat fungsi up() dieksekusi.
     */
    public function down(): void
    {
        Schema::dropIfExists('lsp_report_settings');
        Schema::dropIfExists('participants');
        Schema::dropIfExists('certification_schemes');
    }
};
