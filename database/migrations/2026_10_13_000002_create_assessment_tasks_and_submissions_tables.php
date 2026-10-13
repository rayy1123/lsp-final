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
        if (!Schema::hasTable('assessment_tasks')) {
            Schema::create('assessment_tasks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('scheme_id')->constrained('certification_schemes')->onDelete('cascade');
                $table->string('task_code')->nullable();
                $table->string('title');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('task_submissions')) {
            Schema::create('task_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('task_id')->constrained('assessment_tasks')->onDelete('cascade');
                $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->text('submission_content');
                $table->string('status')->default('Menunggu Peninjauan');
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_submissions');
        Schema::dropIfExists('assessment_tasks');
    }
};
