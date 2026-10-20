<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Existing accounts keep full access forever. Only accounts registered
     * after this feature ships start with a three day trial.
     */
    public function up(): void
    {
        $now = now();

        DB::table('users')->select('id')->orderBy('id')->chunkById(100, function ($users) use ($now): void {
            $rows = [];

            foreach ($users as $user) {
                $rows[] = [
                    'user_id' => $user->id,
                    'plan_id' => null,
                    'source' => 'legacy',
                    'status' => 'active',
                    'starts_at' => $now,
                    'ends_at' => null,
                    'trial_ends_at' => null,
                    'canceled_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if ($rows !== []) {
                DB::table('subscriptions')->insert($rows);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('subscriptions')->where('source', 'legacy')->delete();
    }
};
