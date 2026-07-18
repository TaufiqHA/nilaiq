<?php

namespace Database\Factories;

use App\Models\MidtermExams;
use App\Models\MidtermScores;
use App\Models\Students;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MidtermScores>
 */
class MidtermScoresFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'midterm_exam_id' => MidtermExams::factory(),
            'student_id' => Students::factory(),
            'score' => fake()->randomFloat(2, 0, 100),
            'created_at' => now(),
        ];
    }
}
