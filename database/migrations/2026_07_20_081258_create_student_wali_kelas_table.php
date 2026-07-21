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
        Schema::create('student_wali_kelas', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel class_wali_kelas
            $table->foreignId('class_id')
                ->constrained('class_wali_kelas')
                ->cascadeOnDelete();

            // Identitas Peserta Didik
            $table->string('nis')->unique();
            $table->string('nisn')->unique()->nullable(); // nullable opsional, jaga-jaga jika siswa belum punya NISN
            $table->string('name');
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place');
            $table->date('birth_date')->nullable();
            $table->string('religion');
            $table->string('family_status'); // Anak Kandung, Anak Angkat, dll
            $table->string('child_order'); // Anak ke- (pakai string jaga-jaga format "1 (satu)")
            $table->text('address');
            $table->string('previous_school')->nullable();

            // Status Pendaftaran
            $table->string('registration_status')->nullable();
            $table->string('accepted_class')->nullable();
            $table->date('accepted_date')->nullable();

            // Orang Tua (Bisa diset nullable jika ada siswa yatim/piatu/datanya tidak lengkap)
            $table->string('father_name')->nullable();
            $table->string('father_job')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_job')->nullable();
            $table->text('parent_address')->nullable();
            $table->string('parent_phone')->nullable();

            // Wali (Opsional sesuai keterangan di DBML)
            $table->string('guardian_name')->nullable();
            $table->string('guardian_job')->nullable();
            $table->text('guardian_address')->nullable();
            $table->string('guardian_phone')->nullable();

            // Status Siswa
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_wali_kelas');
    }
};
