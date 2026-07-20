<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\MapelSettings;
use App\Models\SettingsWaliKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapelSettingsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests cannot access mapel settings routes.
     */
    public function test_guest_cannot_access_mapel_settings(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $settingsWaliKelas = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);
        $mapelSetting = MapelSettings::factory()->create(['settingsWaliKelas_id' => $settingsWaliKelas->id]);

        $this->getJson(route('mapel-settings.index'))->assertStatus(401);
        $this->getJson(route('mapel-settings.show', $mapelSetting))->assertStatus(401);
        $this->postJson(route('mapel-settings.store'), [])->assertStatus(401);
        $this->putJson(route('mapel-settings.update', $mapelSetting), [])->assertStatus(401);
        $this->deleteJson(route('mapel-settings.destroy', $mapelSetting))->assertStatus(401);
        $this->deleteJson(route('mapel-settings.delete', $mapelSetting))->assertStatus(401);
    }

    /**
     * Test user without wali_kelas role cannot access mapel settings routes.
     */
    public function test_non_wali_kelas_user_cannot_access_mapel_settings(): void
    {
        $user = User::factory()->create(['role' => 'mapel']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $settingsWaliKelas = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);
        $mapelSetting = MapelSettings::factory()->create(['settingsWaliKelas_id' => $settingsWaliKelas->id]);

        $this->actingAs($user)->getJson(route('mapel-settings.index'))->assertStatus(403);
        $this->actingAs($user)->getJson(route('mapel-settings.show', $mapelSetting))->assertStatus(403);
        $this->actingAs($user)->postJson(route('mapel-settings.store'), [])->assertStatus(403);
        $this->actingAs($user)->putJson(route('mapel-settings.update', $mapelSetting), [])->assertStatus(403);
        $this->actingAs($user)->deleteJson(route('mapel-settings.destroy', $mapelSetting))->assertStatus(403);
        $this->actingAs($user)->deleteJson(route('mapel-settings.delete', $mapelSetting))->assertStatus(403);
    }

    /**
     * Test authenticated wali_kelas user can access mapel settings index JSON.
     */
    public function test_wali_kelas_can_access_mapel_settings_index_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $settingsWaliKelas = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);
        $mapelSetting = MapelSettings::factory()->create([
            'settingsWaliKelas_id' => $settingsWaliKelas->id,
            'mapel' => 'Matematika',
        ]);

        $response = $this->actingAs($user)->getJson(route('mapel-settings.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'mapel' => 'Matematika',
        ]);
    }

    /**
     * Test filtering mapel settings index by settingsWaliKelas_id.
     */
    public function test_wali_kelas_can_filter_mapel_settings_by_settings_wali_kelas_id(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $settings1 = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);
        $settings2 = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);

        $mapel1 = MapelSettings::factory()->create([
            'settingsWaliKelas_id' => $settings1->id,
            'mapel' => 'Fisika',
        ]);
        $mapel2 = MapelSettings::factory()->create([
            'settingsWaliKelas_id' => $settings2->id,
            'mapel' => 'Kimia',
        ]);

        $response = $this->actingAs($user)->getJson(route('mapel-settings.index', ['settingsWaliKelas_id' => $settings1->id]));

        $response->assertStatus(200);
        $response->assertJsonFragment(['mapel' => 'Fisika']);
        $response->assertJsonMissing(['mapel' => 'Kimia']);
    }

    /**
     * Test authenticated wali_kelas user can store mapel settings via JSON.
     */
    public function test_wali_kelas_can_store_mapel_setting_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $settingsWaliKelas = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);

        $data = [
            'settingsWaliKelas_id' => $settingsWaliKelas->id,
            'mapel' => 'Bahasa Indonesia',
            'guru' => 'Budi Utomo, S.Pd',
            'kkm' => 75,
        ];

        $response = $this->actingAs($user)->postJson(route('mapel-settings.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'settingsWaliKelas_id',
                'mapel',
                'guru',
                'kkm',
                'settings_wali_kelas',
            ],
        ]);

        $this->assertDatabaseHas('mapel_settings', [
            'settingsWaliKelas_id' => $settingsWaliKelas->id,
            'mapel' => 'Bahasa Indonesia',
            'guru' => 'Budi Utomo, S.Pd',
            'kkm' => 75,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can show mapel setting via JSON.
     */
    public function test_wali_kelas_can_show_mapel_setting_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $settingsWaliKelas = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);
        $mapelSetting = MapelSettings::factory()->create(['settingsWaliKelas_id' => $settingsWaliKelas->id]);

        $response = $this->actingAs($user)->getJson(route('mapel-settings.show', $mapelSetting));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $mapelSetting->id,
            'mapel' => $mapelSetting->mapel,
            'guru' => $mapelSetting->guru,
            'kkm' => $mapelSetting->kkm,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can update mapel setting via JSON.
     */
    public function test_wali_kelas_can_update_mapel_setting_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $settingsWaliKelas = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);
        $mapelSetting = MapelSettings::factory()->create(['settingsWaliKelas_id' => $settingsWaliKelas->id]);

        $updatedData = [
            'settingsWaliKelas_id' => $settingsWaliKelas->id,
            'mapel' => 'Bahasa Inggris Updated',
            'guru' => 'Jane Doe, M.Pd',
            'kkm' => 80,
        ];

        $response = $this->actingAs($user)->putJson(route('mapel-settings.update', $mapelSetting), $updatedData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data',
        ]);

        $this->assertDatabaseHas('mapel_settings', [
            'id' => $mapelSetting->id,
            'mapel' => 'Bahasa Inggris Updated',
            'guru' => 'Jane Doe, M.Pd',
            'kkm' => 80,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can destroy mapel setting via JSON.
     */
    public function test_wali_kelas_can_destroy_mapel_setting_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $settingsWaliKelas = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);
        $mapelSetting = MapelSettings::factory()->create(['settingsWaliKelas_id' => $settingsWaliKelas->id]);

        $response = $this->actingAs($user)->deleteJson(route('mapel-settings.destroy', $mapelSetting));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Mapel setting deleted successfully.',
        ]);

        $this->assertDatabaseMissing('mapel_settings', [
            'id' => $mapelSetting->id,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can delete mapel setting via alias route.
     */
    public function test_wali_kelas_can_delete_mapel_setting_via_alias(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $settingsWaliKelas = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);
        $mapelSetting = MapelSettings::factory()->create(['settingsWaliKelas_id' => $settingsWaliKelas->id]);

        $response = $this->actingAs($user)->deleteJson(route('mapel-settings.delete', $mapelSetting));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Mapel setting deleted successfully.',
        ]);

        $this->assertDatabaseMissing('mapel_settings', [
            'id' => $mapelSetting->id,
        ]);
    }

    /**
     * Test mapel settings validation errors.
     */
    public function test_mapel_settings_validation_errors(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);

        $response = $this->actingAs($user)->postJson(route('mapel-settings.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'settingsWaliKelas_id',
            'mapel',
            'guru',
            'kkm',
        ]);
    }
}
