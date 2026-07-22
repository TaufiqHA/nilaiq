<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('attendance_meetings', 'tipe')) {
            Schema::table('attendance_meetings', function (Blueprint $table) {
                $table->enum('tipe', ['harian', 'ulang harian', 'tugas', 'pts', 'pas'])
                    ->default('harian');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('attendance_meetings', 'tipe')) {
            Schema::table('attendance_meetings', function (Blueprint $table) {
                $table->dropColumn('tipe');
            });
        }
    }
};
