<?php

use App\Services\LegacySettingsConsolidator;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app(LegacySettingsConsolidator::class)->consolidate();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Removed copies cannot be distinguished from later user edits, so
        // this data consolidation is intentionally irreversible.
    }
};
