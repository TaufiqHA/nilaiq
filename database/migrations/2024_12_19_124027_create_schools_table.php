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
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('address');
            $table->foreignId('academic_years_id')->nullable()->constrained('academic_years')->cascadeOnUpdate();
            $table->string('nss');
            $table->string('npsn');
            $table->string('website')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('principal_name');
            $table->string('nip');
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
