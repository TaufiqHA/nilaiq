<?php

namespace Database\Factories;

use App\Models\Classes;
use App\Models\FinalExams;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FinalExams>
 */
class FinalExamsFactory extends Factory
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
            'exam_date' => fake()->date(),
            'description' => fake()->paragraph(),
        ];
    }
}
