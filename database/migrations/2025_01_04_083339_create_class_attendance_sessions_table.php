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
        Schema::create('class_attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id'); // Foreign key ke tabel classes
            $table->unsignedBigInteger('wali_kelas_id'); // Foreign key ke tabel wali_kelas
            $table->date('date'); // Tanggal absensi
            $table->string('session_name'); // Nama sesi absensi
            $table->timestamps(); // Kolom created_at dan updated_at

            // Foreign key constraints
            $table->foreign('class_id')->references('id')->on('classes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('wali_kelas_id')->references('id')->on('wali_kelas')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_attendance_sessions');
    }
};
