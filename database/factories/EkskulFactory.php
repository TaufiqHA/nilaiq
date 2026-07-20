<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\Ekskul;
use App\Models\StudentWaliKelas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ekskul>
 */
class EkskulFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => function () {
                $user = User::factory()->create(['role' => 'wali_kelas']);
                $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
                $classWaliKelas = ClassWaliKelas::factory()->create([
                    'academic_year_id' => $academicYear->id,
                    'user_id' => $user->id,
                ]);

                return StudentWaliKelas::factory()->create(['class_id' => $classWaliKelas->id])->id;
            },
            'ekskul1' => fake()->optional()->word(),
            'ekskul2' => fake()->optional()->word(),
            'ekskul3' => fake()->optional()->word(),
        ];
    }
}
