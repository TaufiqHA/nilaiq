<?php

namespace Database\Factories;

use App\Models\DailyTestMeetings;
use App\Models\DailyTestScores;
use App\Models\Students;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyTestScores>
 */
class DailyTestScoresFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'daily_test_meeting_id' => DailyTestMeetings::factory(),
            'student_id' => Students::factory(),
            'score' => fake()->randomFloat(2, 0, 100),
            'created_at' => now(),
        ];
    }
}
