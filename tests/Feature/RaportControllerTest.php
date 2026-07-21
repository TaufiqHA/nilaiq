<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\MapelSettings;
use App\Models\SettingsWaliKelas;
use App\Models\StudentWaliKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaportControllerTest extends TestCase
{
    use RefreshDatabase;

    private function setupDataForUser(User $user): array
    {
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);
        $settingsWaliKelas = SettingsWaliKelas::factory()->create([
            'academicYear_id' => $academicYear->id,
        ]);
        $mapel = MapelSettings::factory()->create([
            'settingsWaliKelas_id' => $settingsWaliKelas->id,
            'mapel' => 'Matematika',
        ]);
        $student = StudentWaliKelas::factory()->create([
            'class_id' => $classWaliKelas->id,
        ]);

        return compact('academicYear', 'classWaliKelas', 'settingsWaliKelas', 'mapel', 'student');
    }

    public function test_guest_cannot_access_raport(): void
    {
        $response = $this->get(route('wali-kelas.raport'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_wali_kelas_can_access_raport_index(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $data = $this->setupDataForUser($user);

        $response = $this->actingAs($user)->get(route('wali-kelas.raport'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.waliKelas.raport');
        $response->assertSee($data['student']->name);
    }

    public function test_authenticated_wali_kelas_can_access_single_student_cetak(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $data = $this->setupDataForUser($user);

        $response = $this->actingAs($user)->get(route('wali-kelas.raport.cetak', $data['student']));

        $response->assertStatus(200);
        $response->assertViewIs('auth.waliKelas.raportCetak');
        $response->assertSee(strtoupper($data['student']->name));
    }

    public function test_authenticated_wali_kelas_can_access_cetak_semua(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $data = $this->setupDataForUser($user);

        $response = $this->actingAs($user)->get(route('wali-kelas.raport.cetak-semua'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.waliKelas.raportCetak');
        $response->assertSee(strtoupper($data['student']->name));
    }

    public function test_authenticated_wali_kelas_can_update_mapel_kelompok(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $data = $this->setupDataForUser($user);

        $response = $this->actingAs($user)->post(route('wali-kelas.raport.update-kelompok'), [
            'mapels' => [
                ['id' => $data['mapel']->id, 'kelompok' => 'B'],
            ],
        ]);

        $response->assertRedirect(route('wali-kelas.raport'));
        $this->assertDatabaseHas('mapel_settings', [
            'id' => $data['mapel']->id,
            'kelompok' => 'B',
        ]);
    }
}
