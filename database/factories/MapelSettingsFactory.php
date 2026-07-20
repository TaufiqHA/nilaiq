<?php

namespace Database\Factories;

use App\Models\MapelSettings;
use App\Models\SettingsWaliKelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MapelSettings>
 */
class MapelSettingsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'settingsWaliKelas_id' => SettingsWaliKelas::factory(),
            'mapel' => fake()->word(),
            'guru' => fake()->name(),
            'kkm' => fake()->numberBetween(70, 85),
        ];
    }
}
