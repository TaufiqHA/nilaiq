<?php

namespace Database\Factories;

use App\Models\AssignmentMeetings;
use App\Models\Classes;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssignmentMeetings>
 */
class AssignmentMeetingsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_id' => Classes::factory(),
            'title' => fake()->sentence(3),
            'assignment_date' => fake()->date(),
            'description' => fake()->paragraph(),
        ];
    }
}
