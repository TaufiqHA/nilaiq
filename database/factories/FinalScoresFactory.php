<?php

namespace Database\Factories;

use App\Models\FinalExams;
use App\Models\FinalScores;
use App\Models\Students;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FinalScores>
 */
class FinalScoresFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'final_exam_id' => FinalExams::factory(),
            'student_id' => Students::factory(),
            'score' => fake()->randomFloat(2, 0, 100),
            'created_at' => now(),
        ];
    }
}
