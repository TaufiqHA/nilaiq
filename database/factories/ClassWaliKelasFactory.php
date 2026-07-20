<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassWaliKelas>
 */
class ClassWaliKelasFactory extends Factory
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
            'name' => 'Kelas '.fake()->randomElement(['X', 'XI', 'XII']).' '.fake()->randomElement(['MIPA', 'IPS']).' '.fake()->numberBetween(1, 4),
            'user_id' => User::factory(),
        ];
    }
}
