<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\Sikap;
use App\Models\StudentWaliKelas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sikap>
 */
class SikapFactory extends Factory
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
            'beriman_bertakwa_dan_berakhlak_mulia' => fake()->optional()->sentence(),
            'mandiri' => fake()->optional()->sentence(),
            'bergotong_royong' => fake()->optional()->sentence(),
            'kreatif' => fake()->optional()->sentence(),
            'bernalar_kritis' => fake()->optional()->sentence(),
            'berkebinekaan_global' => fake()->optional()->sentence(),
        ];
    }
}
