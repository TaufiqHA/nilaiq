<?php

namespace Database\Factories;

use App\Models\AttendanceMeetings;
use App\Models\Classes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceMeetings>
 */
class AttendanceMeetingsFactory extends Factory
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
            'meeting_date' => fake()->date(),
            'description' => fake()->paragraph(),
            'tipe' => fake()->randomElement(['harian', 'ulang harian', 'tugas', 'pts', 'pas']),
        ];
    }
}
