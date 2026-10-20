<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Seed the available subscription plans.
     */
    public function run(): void
    {
        Plan::query()->updateOrCreate(
            ['code' => Plan::ANNUAL_CODE],
            [
                'name' => 'NilaiQ Tahunan',
                'price' => 150000,
                'original_price' => 500000,
                'duration_days' => 365,
                'is_active' => true,
            ],
        );
    }
}
