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
        Schema::create('sikaps', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke tabel student_wali_kelas
            $table->foreignId('student_id')
                ->constrained('student_wali_kelas')
                ->onDelete('cascade'); // Menghapus data sikap jika data di student_wali_kelas dihapus

            // Kolom Dimensi Profil Pelajar Pancasila
            $table->text('beriman_bertakwa_dan_berakhlak_mulia')->nullable();
            $table->text('mandiri')->nullable();
            $table->text('bergotong_royong')->nullable();
            $table->text('kreatif')->nullable();
            $table->text('bernalar_kritis')->nullable();
            $table->text('berkebinekaan_global')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sikaps');
    }
};
