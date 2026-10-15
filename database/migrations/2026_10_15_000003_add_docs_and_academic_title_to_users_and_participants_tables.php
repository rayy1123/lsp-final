<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'education_type')) {
                    $table->string('education_type', 50)->default('Siswa/Pelajar')->after('gender');
                }
                if (!Schema::hasColumn('users', 'academic_title')) {
                    $table->string('academic_title', 50)->nullable()->after('education_type');
                }
                if (!Schema::hasColumn('users', 'id_card_file')) {
                    $table->string('id_card_file', 255)->nullable()->after('address');
                }
                if (!Schema::hasColumn('users', 'report_sheet_file')) {
                    $table->string('report_sheet_file', 255)->nullable()->after('id_card_file');
                }
                if (!Schema::hasColumn('users', 'certificate_file')) {
                    $table->string('certificate_file', 255)->nullable()->after('report_sheet_file');
                }
                if (!Schema::hasColumn('users', 'diploma_file')) {
                    $table->string('diploma_file', 255)->nullable()->after('certificate_file');
                }
                if (!Schema::hasColumn('users', 'photo_file')) {
                    $table->string('photo_file', 255)->nullable()->after('diploma_file');
                }
                if (!Schema::hasColumn('users', 'portfolio_file')) {
                    $table->string('portfolio_file', 255)->nullable()->after('photo_file');
                }
            });
        }

        if (Schema::hasTable('participants')) {
            Schema::table('participants', function (Blueprint $table) {
                if (!Schema::hasColumn('participants', 'education_type')) {
                    $table->string('education_type', 50)->default('Siswa/Pelajar')->after('gender');
                }
                if (!Schema::hasColumn('participants', 'academic_title')) {
                    $table->string('academic_title', 50)->nullable()->after('education_type');
                }
                if (!Schema::hasColumn('participants', 'id_card_file')) {
                    $table->string('id_card_file', 255)->nullable()->after('address');
                }
                if (!Schema::hasColumn('participants', 'report_sheet_file')) {
                    $table->string('report_sheet_file', 255)->nullable()->after('id_card_file');
                }
                if (!Schema::hasColumn('participants', 'certificate_file')) {
                    $table->string('certificate_file', 255)->nullable()->after('report_sheet_file');
                }
                if (!Schema::hasColumn('participants', 'diploma_file')) {
                    $table->string('diploma_file', 255)->nullable()->after('certificate_file');
                }
                if (!Schema::hasColumn('participants', 'photo_file')) {
                    $table->string('photo_file', 255)->nullable()->after('diploma_file');
                }
                if (!Schema::hasColumn('participants', 'portfolio_file')) {
                    $table->string('portfolio_file', 255)->nullable()->after('photo_file');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                foreach (['portfolio_file', 'photo_file', 'diploma_file', 'certificate_file', 'report_sheet_file', 'id_card_file', 'academic_title', 'education_type'] as $col) {
                    if (Schema::hasColumn('users', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('participants')) {
            Schema::table('participants', function (Blueprint $table) {
                foreach (['portfolio_file', 'photo_file', 'diploma_file', 'certificate_file', 'report_sheet_file', 'id_card_file', 'academic_title', 'education_type'] as $col) {
                    if (Schema::hasColumn('participants', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
