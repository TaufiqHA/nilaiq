<?php

namespace Database\Factories;

use App\Models\Settings;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Settings>
 */
class SettingsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_name' => fake()->company().' School',
            'npsn' => fake()->numerify('########'),
            'school_address' => fake()->address(),
            'principal_name' => fake()->name(),
            'school_logo' => 'logos/'.fake()->word().'.png',
            'teacher_name' => fake()->name(),
            'teacher_nip' => fake()->numerify('##################'),
            'teacher_email' => fake()->safeEmail(),
            'teacher_phone' => fake()->phoneNumber(),
            'subject_name' => fake()->randomElement(['Matematika', 'Fisika', 'Kimia', 'Biologi', 'Bahasa Indonesia', 'Bahasa Inggris', 'Sejarah']),
            'minimum_score' => fake()->randomFloat(2, 60, 85),
            'daily_test_weight' => 30,
            'assignment_weight' => 20,
            'midterm_weight' => 25,
            'final_weight' => 25,
        ];
    }
}
