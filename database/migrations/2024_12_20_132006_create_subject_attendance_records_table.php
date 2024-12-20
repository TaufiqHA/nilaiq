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
        Schema::create('subject_attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_session_id'); // Foreign key ke tabel subject_attendance_sessions
            $table->unsignedBigInteger('student_id'); // Foreign key ke tabel students
            $table->enum('status', ['Present', 'Sick', 'Permission', 'Absent']); // Status kehadiran
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps(); // Waktu pencatatan dan pembaruan

            // Define foreign key constraints
            $table->foreign('attendance_session_id')->references('id')->on('subject_attendance_sessions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_attendance_records');
    }
};
