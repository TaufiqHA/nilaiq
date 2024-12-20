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
        Schema::create('guru_mata_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id'); // Foreign key to teachers table
            $table->unsignedBigInteger('subject_id'); // Foreign key to subjects table

            // Define foreign key constraints
            $table->foreign('teacher_id')->references('id')->on('teachers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('subject_id')->references('id')->on('subjects')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_mata_pelajarans');
    }
};
