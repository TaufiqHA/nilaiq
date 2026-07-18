<?php

namespace Database\Factories;

use App\Models\AssignmentMeetings;
use App\Models\AssignmentScores;
use App\Models\Students;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssignmentScores>
 */
class AssignmentScoresFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assignment_meeting_id' => AssignmentMeetings::factory(),
            'student_id' => Students::factory(),
            'score' => fake()->randomFloat(2, 0, 100),
            'created_at' => now(),
        ];
    }
}
