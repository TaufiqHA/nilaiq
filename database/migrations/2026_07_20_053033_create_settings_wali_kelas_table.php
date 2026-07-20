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
        Schema::create('settings_wali_kelas', function (Blueprint $table) {
            $table->id();
            // School Info
            $table->string('school_name');
            $table->string('npsn');
            $table->text('school_address');

            $table->string('principal_name');
            $table->string('school_logo')->nullable();

            // Teacher Info
            $table->string('teacher_name');
            $table->string('teacher_nip')->nullable();
            $table->string('teacher_email')->nullable();
            $table->string('teacher_phone')->nullable();

            // Foreign Key
            // cascadeOnDelete() opsional, sesuaikan dengan kebutuhan bisnismu
            $table->foreignId('academicYear_id')
                ->constrained('academic_years')
                ->cascadeOnDelete();

            $table->date('tanggal_penerimaan_rapor');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_wali_kelas');
    }
};
