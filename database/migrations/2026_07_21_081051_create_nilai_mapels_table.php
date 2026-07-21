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
        Schema::create('nilai_mapels', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel student_wali_kelas
            $table->foreignId('student_id')
                ->constrained('student_wali_kelas')
                ->onDelete('cascade');

            // Foreign Key ke tabel mapel_settings
            $table->foreignId('mapel_id')
                ->constrained('mapel_settings')
                ->onDelete('cascade');

            // Nilai dan Capaian Pembelajaran
            $table->integer('nilai')->nullable();
            $table->string('capaian')->nullable(); // Default varchar di Laravel berukuran 255 karakter
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_mapels');
    }
};
