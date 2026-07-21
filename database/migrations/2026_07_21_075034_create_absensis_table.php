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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel student_wali_kelas
            $table->foreignId('student_id')
                ->constrained('student_wali_kelas')
                ->onDelete('cascade');

            // Kolom Rekap Absensi
            $table->integer('hadir')->default(0);
            $table->integer('izin')->default(0);
            $table->integer('sakit')->default(0);
            $table->integer('alpa')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
