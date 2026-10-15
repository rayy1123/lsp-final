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
                if (!Schema::hasColumn('users', 'nik')) {
                    $table->string('nik', 16)->nullable()->after('name');
                }
                if (!Schema::hasColumn('users', 'gender')) {
                    $table->string('gender', 20)->nullable()->after('nik');
                }
            });
        }

        if (Schema::hasTable('participants')) {
            Schema::table('participants', function (Blueprint $table) {
                if (!Schema::hasColumn('participants', 'nik')) {
                    $table->string('nik', 16)->nullable()->after('registration_number');
                }
                if (!Schema::hasColumn('participants', 'gender')) {
                    $table->string('gender', 20)->nullable()->after('nik');
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
                if (Schema::hasColumn('users', 'gender')) {
                    $table->dropColumn('gender');
                }
                if (Schema::hasColumn('users', 'nik')) {
                    $table->dropColumn('nik');
                }
            });
        }

        if (Schema::hasTable('participants')) {
            Schema::table('participants', function (Blueprint $table) {
                if (Schema::hasColumn('participants', 'gender')) {
                    $table->dropColumn('gender');
                }
                if (Schema::hasColumn('participants', 'nik')) {
                    $table->dropColumn('nik');
                }
            });
        }
    }
};
