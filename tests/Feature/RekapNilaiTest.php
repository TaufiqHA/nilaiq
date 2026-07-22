<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\MapelSettings;
use App\Models\NilaiMapel;
use App\Models\SettingsWaliKelas;
use App\Models\StudentWaliKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapNilaiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest cannot access rekap nilai page.
     */
    public function test_guest_cannot_access_rekap_nilai(): void
    {
        $response = $this->get(route('wali-kelas.rekap-nilai'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test non-wali_kelas user cannot access rekap nilai page.
     */
    public function test_non_wali_kelas_user_cannot_access_rekap_nilai(): void
    {
        $user = User::factory()->create(['role' => 'mapel']);

        $response = $this->actingAs($user)->get(route('wali-kelas.rekap-nilai'));

        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test authenticated wali_kelas user can access rekap nilai page and view scores and rankings.
     */
    public function test_wali_kelas_user_can_access_rekap_nilai_and_view_grades(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);

        $academicYear = AcademicYear::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
            'name' => 'Kelas X MIPA 1',
        ]);

        $settingsWaliKelas = SettingsWaliKelas::factory()->create([
            'academicYear_id' => $academicYear->id,
        ]);

        $mapel = MapelSettings::factory()->create([
            'settingsWaliKelas_id' => $settingsWaliKelas->id,
            'mapel' => 'Matematika',
            'guru' => 'Budi',
            'kkm' => 75,
        ]);

        $student1 = StudentWaliKelas::factory()->create([
            'class_id' => $classWaliKelas->id,
            'name' => 'Ahmad Fadhil',
            'nis' => '1111',
            'nisn' => '001111',
            'status' => 'ACTIVE',
        ]);

        $student2 = StudentWaliKelas::factory()->create([
            'class_id' => $classWaliKelas->id,
            'name' => 'Siti Aminah',
            'nis' => '2222',
            'nisn' => '002222',
            'status' => 'ACTIVE',
        ]);

        NilaiMapel::factory()->create([
            'student_id' => $student1->id,
            'mapel_id' => $mapel->id,
            'nilai' => 85,
        ]);

        NilaiMapel::factory()->create([
            'student_id' => $student2->id,
            'mapel_id' => $mapel->id,
            'nilai' => 90,
        ]);

        $response = $this->actingAs($user)->get(route('wali-kelas.rekap-nilai'));

        $response->assertStatus(200);
        $response->assertSee('Ahmad Fadhil');
        $response->assertSee('Siti Aminah');
        $response->assertSee('Matematika');
        $response->assertSee('Kelas X MIPA 1');

        // Assert scores are rendered
        $response->assertSee('85');
        $response->assertSee('90');
    }
}
