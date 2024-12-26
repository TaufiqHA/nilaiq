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
        Schema::create('score_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('score_session_id')->constrained('score_sessions')->cascadeOnDelete()->cascadeOnUpdate(); // Foreign key ke score_sessions
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete()->cascadeOnUpdate(); // Foreign key ke students
            $table->integer('score'); // Nilai tunggal
            $table->text('notes')->nullable(); // Catatan guru (nullable)
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('score_records');
    }
};
