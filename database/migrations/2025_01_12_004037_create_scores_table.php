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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id'); // Foreign key ke tabel students
            $table->unsignedBigInteger('class_id'); // Foreign key ke tabel classes
            $table->unsignedBigInteger('subject_id'); // Foreign key ke tabel subjects
            $table->unsignedBigInteger('teacher_id'); // Foreign key ke tabel teachers
            $table->unsignedBigInteger('academic_year_id'); // Foreign key ke tabel academic_years
            $table->unsignedBigInteger('semester_id'); // Foreign key ke tabel semesters
            $table->integer('score'); // Kolom score
            $table->text('teacher_notes')->nullable(); // Catatan guru (nullable)
            $table->timestamps(); // Kolom created_at dan updated_at

            // Foreign key constraints
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('teacher_id')->references('id')->on('teachers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('semester_id')->references('id')->on('semesters')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
