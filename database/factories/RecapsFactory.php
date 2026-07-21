<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Recaps;
use App\Models\Students;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Recaps>
 */
class RecapsFactory extends Factory
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
            'class_id' => Classes::factory(),
            'student_id' => Students::factory(),
            'final_score_result' => fake()->randomFloat(2, 60, 100),
            'competency_description' => fake()->sentence(),
        ];
    }
}
