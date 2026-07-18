<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Classes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Classes>
 */
class ClassesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'name' => 'Kelas '.fake()->randomElement(['X', 'XI', 'XII']).' '.fake()->randomElement(['MIPA', 'IPS', 'Bahasa']).' '.fake()->numberBetween(1, 4),
        ];
    }
}
