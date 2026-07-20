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
        Schema::create('ekskuls', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel student_wali_kelas
            $table->foreignId('student_id')
                ->constrained('student_wali_kelas')
                ->cascadeOnDelete();

            // Data Ekstrakurikuler
            // Dibuat nullable karena siswa mungkin tidak mengikuti 3 ekskul sekaligus (atau bahkan tidak ikut sama sekali)
            $table->string('ekskul1')->nullable();
            $table->string('ekskul2')->nullable();
            $table->string('ekskul3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekskuls');
    }
};
