<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\SettingsWaliKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsWaliKelasTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login for settings wali kelas routes.
     */
    public function test_guest_cannot_access_settings_wali_kelas(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $setting = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);

        $this->getJson(route('settings-wali-kelas.index'))->assertStatus(401);
        $this->getJson(route('settings-wali-kelas.show', $setting))->assertStatus(401);
        $this->postJson(route('settings-wali-kelas.store'), [])->assertStatus(401);
        $this->putJson(route('settings-wali-kelas.update', $setting), [])->assertStatus(401);
        $this->deleteJson(route('settings-wali-kelas.destroy', $setting))->assertStatus(401);
        $this->deleteJson(route('settings-wali-kelas.delete', $setting))->assertStatus(401);
    }

    /**
     * Test authenticated user can access settings wali kelas index JSON.
     */
    public function test_authenticated_user_can_access_settings_wali_kelas_index_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $setting = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);

        $response = $this->actingAs($user)->getJson(route('settings-wali-kelas.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'school_name' => $setting->school_name,
        ]);
    }

    /**
     * Test authenticated user can store settings wali kelas via JSON.
     */
    public function test_authenticated_user_can_store_settings_wali_kelas_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);

        $data = [
            'school_name' => 'SMA Negeri 1 Jakarta',
            'npsn' => '12345678',
            'school_address' => 'Jl. Merdeka No. 1',
            'principal_name' => 'Dr. H. Ahmad',
            'teacher_name' => 'Budi Santoso, S.Pd',
            'teacher_nip' => '198501012010011001',
            'teacher_email' => 'budi@school.sch.id',
            'teacher_phone' => '081234567890',
            'academicYear_id' => $academicYear->id,
            'tanggal_penerimaan_rapor' => '2026-06-25',
        ];

        $response = $this->actingAs($user)->postJson(route('settings-wali-kelas.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'school_name',
                'npsn',
                'academic_year',
            ],
        ]);

        $this->assertDatabaseHas('settings_wali_kelas', [
            'school_name' => 'SMA Negeri 1 Jakarta',
            'npsn' => '12345678',
        ]);
    }

    /**
     * Test authenticated user can view specific settings wali kelas via JSON.
     */
    public function test_authenticated_user_can_show_settings_wali_kelas_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $setting = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);

        $response = $this->actingAs($user)->getJson(route('settings-wali-kelas.show', $setting));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $setting->id,
            'school_name' => $setting->school_name,
        ]);
    }

    /**
     * Test authenticated user can update settings wali kelas via JSON.
     */
    public function test_authenticated_user_can_update_settings_wali_kelas_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $setting = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);

        $updatedData = [
            'school_name' => 'SMA Negeri 2 Jakarta Updated',
            'npsn' => '87654321',
            'school_address' => 'Jl. Pemuda No. 2',
            'principal_name' => 'Drs. Supriadi',
            'teacher_name' => 'Siti Rahma, M.Pd',
            'teacher_nip' => '199001012015012002',
            'teacher_email' => 'siti@school.sch.id',
            'teacher_phone' => '089876543210',
            'academicYear_id' => $academicYear->id,
            'tanggal_penerimaan_rapor' => '2026-12-20',
        ];

        $response = $this->actingAs($user)->putJson(route('settings-wali-kelas.update', $setting), $updatedData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data',
        ]);

        $this->assertDatabaseHas('settings_wali_kelas', [
            'id' => $setting->id,
            'school_name' => 'SMA Negeri 2 Jakarta Updated',
        ]);
    }

    /**
     * Test authenticated user can destroy settings wali kelas via JSON.
     */
    public function test_authenticated_user_can_destroy_settings_wali_kelas_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $setting = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);

        $response = $this->actingAs($user)->deleteJson(route('settings-wali-kelas.destroy', $setting));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Settings wali kelas deleted successfully.',
        ]);

        $this->assertDatabaseMissing('settings_wali_kelas', [
            'id' => $setting->id,
        ]);
    }

    /**
     * Test authenticated user can delete settings wali kelas via alias route.
     */
    public function test_authenticated_user_can_delete_settings_wali_kelas_via_alias(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $setting = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);

        $response = $this->actingAs($user)->deleteJson(route('settings-wali-kelas.delete', $setting));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Settings wali kelas deleted successfully.',
        ]);

        $this->assertDatabaseMissing('settings_wali_kelas', [
            'id' => $setting->id,
        ]);
    }

    /**
     * Test settings wali kelas validation errors.
     */
    public function test_settings_wali_kelas_validation_errors(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);

        $response = $this->actingAs($user)->postJson(route('settings-wali-kelas.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'school_name',
            'npsn',
            'school_address',
            'principal_name',
            'teacher_name',
            'academicYear_id',
            'tanggal_penerimaan_rapor',
        ]);
    }
}
