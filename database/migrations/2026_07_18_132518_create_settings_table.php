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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->string('school_name')->nullable();
            $table->string('npsn')->nullable();
            $table->text('school_address')->nullable();
            $table->string('principal_name')->nullable();
            $table->string('school_logo')->nullable();

            $table->string('teacher_name')->nullable();
            $table->string('teacher_nip')->nullable();
            $table->string('teacher_email')->nullable();
            $table->string('teacher_phone')->nullable();

            $table->string('subject_name')->nullable();
            $table->decimal('minimum_score', 5, 2)->nullable();

            $table->decimal('daily_test_weight', 5, 2)->nullable();
            $table->decimal('assignment_weight', 5, 2)->nullable();
            $table->decimal('midterm_weight', 5, 2)->nullable();
            $table->decimal('final_weight', 5, 2)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
