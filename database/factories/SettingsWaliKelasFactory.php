<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\SettingsWaliKelas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<SettingsWaliKelas>
 */
class SettingsWaliKelasFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Model>
     */
    protected $model = SettingsWaliKelas::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_name' => fake()->company().' School',
            'npsn' => fake()->numerify('########'),
            'school_address' => fake()->address(),
            'principal_name' => fake()->name(),
            'school_logo' => 'logos/'.fake()->word().'.png',
            'teacher_name' => fake()->name(),
            'teacher_nip' => fake()->numerify('##################'),
            'teacher_email' => fake()->safeEmail(),
            'teacher_phone' => fake()->phoneNumber(),
            'academicYear_id' => AcademicYear::factory(['user_id' => User::factory()]),
            'tanggal_penerimaan_rapor' => fake()->date(),
        ];
    }
}
