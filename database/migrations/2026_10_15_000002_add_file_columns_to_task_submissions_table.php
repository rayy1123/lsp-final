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
        if (Schema::hasTable('task_submissions')) {
            Schema::table('task_submissions', function (Blueprint $table) {
                if (!Schema::hasColumn('task_submissions', 'file_path')) {
                    $table->string('file_path')->nullable()->after('submission_content');
                }
                if (!Schema::hasColumn('task_submissions', 'file_name')) {
                    $table->string('file_name')->nullable()->after('file_path');
                }
                if (!Schema::hasColumn('task_submissions', 'file_type')) {
                    $table->string('file_type', 50)->nullable()->after('file_name');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('task_submissions')) {
            Schema::table('task_submissions', function (Blueprint $table) {
                if (Schema::hasColumn('task_submissions', 'file_type')) {
                    $table->dropColumn('file_type');
                }
                if (Schema::hasColumn('task_submissions', 'file_name')) {
                    $table->dropColumn('file_name');
                }
                if (Schema::hasColumn('task_submissions', 'file_path')) {
                    $table->dropColumn('file_path');
                }
            });
        }
    }
};
