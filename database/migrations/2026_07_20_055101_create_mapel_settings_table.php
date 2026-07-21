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
        Schema::create('mapel_settings', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel settings_wali_kelas
            $table->foreignId('settingsWaliKelas_id')
                ->constrained('settings_wali_kelas')
                ->cascadeOnDelete();

            // Data Mata Pelajaran
            $table->string('mapel');
            $table->string('guru');
            $table->integer('kkm');
            $table->string('kelompok')->default('A');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapel_settings');
    }
};
