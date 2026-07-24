<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\MapelSettings;
use App\Models\SettingsWaliKelas;
use App\Models\Sikap;
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

    public function test_raport_cetak_displays_sikap_data_correctly(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $data = $this->setupDataForUser($user);

        // 1. Case where Sikap is not filled (should show '-')
        $response = $this->actingAs($user)->get(route('wali-kelas.raport.cetak', $data['student']));
        $response->assertStatus(200);
        $response->assertSee('-');

        // 2. Case where Sikap is filled
        $sikap = Sikap::factory()->create([
            'student_id' => $data['student']->id,
            'beriman_bertakwa_dan_berakhlak_mulia' => 'Sikap Sangat Beriman dan Bertakwa',
            'mandiri' => 'Sikap Sangat Mandiri',
            'bergotong_royong' => 'Sikap Gotong Royong Bagus',
            'kreatif' => 'Sikap Sangat Kreatif',
            'bernalar_kritis' => 'Sikap Bernalar Kritis Hebat',
            'berkebinekaan_global' => 'Sikap Berkebinekaan Global Baik',
        ]);

        $response2 = $this->actingAs($user)->get(route('wali-kelas.raport.cetak', $data['student']));
        $response2->assertStatus(200);
        $response2->assertSee('Sikap Sangat Beriman dan Bertakwa');
        $response2->assertSee('Sikap Sangat Mandiri');
        $response2->assertSee('Sikap Gotong Royong Bagus');
        $response2->assertSee('Sikap Sangat Kreatif');
        $response2->assertSee('Sikap Bernalar Kritis Hebat');
        $response2->assertSee('Sikap Berkebinekaan Global Baik');
    }

    public function test_raport_cetak_status_box_visibility_based_on_semester(): void
    {
        $userGenap = User::factory()->create(['role' => 'wali_kelas']);

        // Test GENAP Semester (should see status box)
        $dataGenap = $this->setupDataForUser($userGenap);
        $dataGenap['academicYear']->update(['semester' => 'GENAP']);

        $responseGenap = $this->actingAs($userGenap)->get(route('wali-kelas.raport.cetak', $dataGenap['student']));
        $responseGenap->assertStatus(200);
        $responseGenap->assertSee('Telah menyelesaikan seluruh rangkaian pembelajaran');
        $responseGenap->assertSee('LULUS');

        $userGanjil = User::factory()->create(['role' => 'wali_kelas']);

        // Test GANJIL Semester (should NOT see status box)
        $dataGanjil = $this->setupDataForUser($userGanjil);
        $dataGanjil['academicYear']->update(['semester' => 'GANJIL']);

        $responseGanjil = $this->actingAs($userGanjil)->get(route('wali-kelas.raport.cetak', $dataGanjil['student']));
        $responseGanjil->assertStatus(200);
        $responseGanjil->assertDontSee('Telah menyelesaikan seluruh rangkaian pembelajaran');
        $responseGanjil->assertDontSee('dinyatakan LULUS');
    }
}
