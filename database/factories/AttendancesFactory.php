<?php

namespace Database\Factories;

use App\Models\AttendanceMeetings;
use App\Models\Attendances;
use App\Models\Students;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendances>
 */
class AttendancesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attendance_meeting_id' => AttendanceMeetings::factory(),
            'student_id' => Students::factory(),
            'status' => fake()->randomElement(['HADIR', 'IZIN', 'SAKIT', 'ALFA']),
            'note' => fake()->optional()->sentence(),
        ];
    }
}
