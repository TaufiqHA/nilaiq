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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nis');
            $table->enum('gender', ['Laki-Laki', 'Perempuan']);
            $table->foreignId('class_id')->nullable()->constrained('classes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('nisn')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion')->nullable();
            $table->string('family_status')->nullable();
            $table->integer('child_order')->nullable();
            $table->text('address')->nullable();
            $table->string('origin_school')->nullable();
            $table->string('registration_status')->nullable();
            $table->string('accepted_in_class')->nullable();
            $table->date('admission_date')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->text('parent_address')->nullable();
            $table->string('father_job')->nullable();
            $table->string('mother_job')->nullable();
            $table->string('parent_phone')->nullable();
            $table->string('guardian_name')->nullable();
            $table->text('guardian_address')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_job')->nullable();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
