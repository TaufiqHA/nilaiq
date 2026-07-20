<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\Prestasi;
use App\Models\StudentWaliKelas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prestasi>
 */
class PrestasiFactory extends Factory
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
            'prestasi1' => fake()->optional()->numberBetween(1, 3),
            'catatan_prestasi1' => fake()->optional()->sentence(),
            'prestasi2' => fake()->optional()->numberBetween(1, 3),
            'catatan_prestasi2' => fake()->optional()->sentence(),
            'prestasi3' => fake()->optional()->numberBetween(1, 3),
            'catatan_prestasi3' => fake()->optional()->sentence(),
        ];
    }
}
