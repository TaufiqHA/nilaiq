<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The settings table used to be global. Copy the existing row to every
     * teacher so each account starts with its own isolated master data.
     */
    public function up(): void
    {
        $global = DB::table('settings')->whereNull('user_id')->first();

        if ($global === null) {
            return;
        }

        $template = (array) $global;
        unset($template['id'], $template['user_id']);
        $template['created_at'] = $template['created_at'] ?? now();
        $template['updated_at'] = now();

        $userIds = DB::table('users')
            ->where('role', '!=', 'admin')
            ->pluck('id');

        foreach ($userIds as $userId) {
            $exists = DB::table('settings')->where('user_id', $userId)->exists();

            if ($exists) {
                continue;
            }

            DB::table('settings')->insert([
                ...$template,
                'user_id' => $userId,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The copied rows cannot be distinguished from rows created by users
        // after the migration, so the data migration is intentionally irreversible.
    }
};
