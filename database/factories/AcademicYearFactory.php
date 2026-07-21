<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startYear = fake()->numberBetween(2020, 2030);

        return [
            'user_id' => User::factory(),
            'year' => "{$startYear}/".($startYear + 1),
            'semester' => fake()->randomElement(['GANJIL', 'GENAP']),
            'is_active' => false,
        ];
    }

    /**
     * Indicate that the academic year is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}
