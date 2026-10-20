<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => Plan::ANNUAL_CODE,
            'name' => 'NilaiQ Tahunan',
            'price' => 150000,
            'original_price' => 500000,
            'duration_days' => 365,
            'is_active' => true,
        ];
    }
}
