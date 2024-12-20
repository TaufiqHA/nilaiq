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
        Schema::create('subject_attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subject_id'); // Mata pelajaran yang diabsen
            $table->unsignedBigInteger('class_id'); // Foreign key ke tabel classes
            $table->unsignedBigInteger('teacher_id'); // Guru pengampu mata pelajaran
            $table->date('date'); // Tanggal absensi
            $table->string('session_name'); // Nama sesi absensi
            $table->timestamps(); // Waktu pencatatan dan pembaruan

            // Define foreign key constraints
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('teacher_id')->references('id')->on('teachers')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_attendance_sessions');
    }
};
