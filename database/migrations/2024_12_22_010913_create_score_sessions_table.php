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
        Schema::create('score_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete()->cascadeOnUpdate(); // Foreign key ke tabel subjects
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete()->cascadeOnUpdate(); // Foreign key ke tabel classes
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete()->cascadeOnUpdate(); // Foreign key ke tabel teachers
            $table->enum('score_type', ['Daily', 'Midterm', 'Final']); // Jenis penilaian
            $table->string('session_name'); // Nama sesi penilaian
            $table->date('date'); // Tanggal penilaian
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('score_sessions');
    }
};
