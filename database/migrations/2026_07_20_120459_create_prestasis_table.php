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
        Schema::create('prestasis', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel student_wali_kelas
            $table->foreignId('student_id')
                ->constrained('student_wali_kelas')
                ->cascadeOnDelete();

            // Data Prestasi
            // Dibuat nullable karena tidak semua siswa memiliki prestasi,
            // atau mungkin hanya memiliki 1 atau 2 prestasi saja.
            $table->integer('prestasi1')->nullable();
            $table->string('catatan_prestasi1')->nullable();

            $table->integer('prestasi2')->nullable();
            $table->string('catatan_prestasi2')->nullable();

            $table->integer('prestasi3')->nullable();
            $table->string('catatan_prestasi3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasis');
    }
};
