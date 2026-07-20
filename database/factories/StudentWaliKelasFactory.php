<?php

namespace Database\Factories;

use App\Models\ClassWaliKelas;
use App\Models\StudentWaliKelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentWaliKelas>
 */
class StudentWaliKelasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_id' => ClassWaliKelas::factory(),
            'nis' => fake()->unique()->numerify('##########'),
            'nisn' => fake()->unique()->numerify('##########'),
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['L', 'P']),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->date(),
            'religion' => fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu']),
            'family_status' => 'Anak Kandung',
            'child_order' => '1',
            'address' => fake()->address(),
            'previous_school' => fake()->company(),
            'registration_status' => 'Siswa Baru',
            'accepted_class' => 'X',
            'accepted_date' => fake()->date(),
            'father_name' => fake()->name('male'),
            'father_job' => fake()->jobTitle(),
            'mother_name' => fake()->name('female'),
            'mother_job' => fake()->jobTitle(),
            'parent_address' => fake()->address(),
            'parent_phone' => fake()->phoneNumber(),
            'guardian_name' => null,
            'guardian_job' => null,
            'guardian_address' => null,
            'guardian_phone' => null,
            'status' => 'ACTIVE',
        ];
    }

    /**
     * Indicate that the student is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ACTIVE',
        ]);
    }

    /**
     * Indicate that the student is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'INACTIVE',
        ]);
    }
}
