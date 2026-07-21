<?php

namespace Database\Factories;

use App\Models\MapelSettings;
use App\Models\NilaiMapel;
use App\Models\StudentWaliKelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NilaiMapel>
 */
class NilaiMapelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => StudentWaliKelas::factory(),
            'mapel_id' => MapelSettings::factory(),
            'nilai' => fake()->optional()->numberBetween(60, 100),
            'capaian' => fake()->optional()->sentence(),
        ];
    }
}
