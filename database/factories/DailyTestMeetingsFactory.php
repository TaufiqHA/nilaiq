<?php

namespace Database\Factories;

use App\Models\Classes;
use App\Models\DailyTestMeetings;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyTestMeetings>
 */
class DailyTestMeetingsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_id' => Classes::factory(),
            'title' => fake()->sentence(3),
            'test_date' => fake()->date(),
            'description' => fake()->paragraph(),
        ];
    }
}
