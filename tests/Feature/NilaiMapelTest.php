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

class NilaiMapelTest extends TestCase
{
    use RefreshDatabase;

    private function createStudent(User $user): StudentWaliKelas
    {
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);

        return StudentWaliKelas::factory()->create(['class_id' => $classWaliKelas->id]);
    }

    private function createMapel(User $user): MapelSettings
    {
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $settingsWaliKelas = SettingsWaliKelas::factory()->create(['academicYear_id' => $academicYear->id]);

        return MapelSettings::factory()->create(['settingsWaliKelas_id' => $settingsWaliKelas->id]);
    }

    /**
     * Test guests cannot access nilai mapel routes.
     */
    public function test_guest_cannot_access_nilai_mapel(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $mapel = $this->createMapel($user);
        $nilaiMapel = NilaiMapel::factory()->create([
            'student_id' => $student->id,
            'mapel_id' => $mapel->id,
        ]);

        $this->getJson(route('wali-kelas.nilai-mapels.index'))->assertStatus(401);
        $this->getJson(route('wali-kelas.nilai-mapels.show', $nilaiMapel))->assertStatus(401);
        $this->postJson(route('wali-kelas.nilai-mapels.store'), [])->assertStatus(401);
        $this->putJson(route('wali-kelas.nilai-mapels.update', $nilaiMapel), [])->assertStatus(401);
        $this->deleteJson(route('wali-kelas.nilai-mapels.destroy', $nilaiMapel))->assertStatus(401);
        $this->deleteJson(route('wali-kelas.nilai-mapels.delete', $nilaiMapel))->assertStatus(401);
    }

    /**
     * Test non-wali_kelas user cannot access nilai mapel routes.
     */
    public function test_non_wali_kelas_user_cannot_access_nilai_mapel(): void
    {
        $nonWaliKelasUser = User::factory()->create(['role' => 'mapel']);
        $waliKelasUser = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($waliKelasUser);
        $mapel = $this->createMapel($waliKelasUser);
        $nilaiMapel = NilaiMapel::factory()->create([
            'student_id' => $student->id,
            'mapel_id' => $mapel->id,
        ]);

        $this->actingAs($nonWaliKelasUser)->getJson(route('wali-kelas.nilai-mapels.index'))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->getJson(route('wali-kelas.nilai-mapels.show', $nilaiMapel))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->postJson(route('wali-kelas.nilai-mapels.store'), [])->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->putJson(route('wali-kelas.nilai-mapels.update', $nilaiMapel), [])->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->deleteJson(route('wali-kelas.nilai-mapels.destroy', $nilaiMapel))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->deleteJson(route('wali-kelas.nilai-mapels.delete', $nilaiMapel))->assertStatus(403);
    }

    /**
     * Test authenticated wali_kelas user can access nilai mapel index JSON.
     */
    public function test_wali_kelas_can_access_nilai_mapel_index_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $mapel = $this->createMapel($user);
        $nilaiMapel = NilaiMapel::factory()->create([
            'student_id' => $student->id,
            'mapel_id' => $mapel->id,
        ]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.nilai-mapels.index'));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $nilaiMapel->id,
                'student_id' => $student->id,
                'mapel_id' => $mapel->id,
            ]);
    }

    /**
     * Test authenticated wali_kelas user can filter nilai mapel by student_id and mapel_id.
     */
    public function test_wali_kelas_can_filter_nilai_mapel_by_student_id_and_mapel_id(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student1 = $this->createStudent($user);
        $student2 = $this->createStudent($user);
        $mapel1 = $this->createMapel($user);
        $mapel2 = $this->createMapel($user);

        $nilai1 = NilaiMapel::factory()->create(['student_id' => $student1->id, 'mapel_id' => $mapel1->id]);
        $nilai2 = NilaiMapel::factory()->create(['student_id' => $student2->id, 'mapel_id' => $mapel2->id]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.nilai-mapels.index', [
            'student_id' => $student1->id,
            'mapel_id' => $mapel1->id,
        ]));

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $nilai1->id])
            ->assertJsonMissing(['id' => $nilai2->id]);
    }

    /**
     * Test authenticated wali_kelas user can store nilai mapel via JSON.
     */
    public function test_wali_kelas_can_store_nilai_mapel_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $mapel = $this->createMapel($user);

        $payload = [
            'student_id' => $student->id,
            'mapel_id' => $mapel->id,
            'nilai' => 85,
            'capaian' => 'Menunjukkan penguasaan materi yang baik.',
        ];

        $response = $this->actingAs($user)->postJson(route('wali-kelas.nilai-mapels.store'), $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Nilai mapel created successfully.',
                'student_id' => $student->id,
                'mapel_id' => $mapel->id,
                'nilai' => 85,
            ]);

        $this->assertDatabaseHas('nilai_mapels', [
            'student_id' => $student->id,
            'mapel_id' => $mapel->id,
            'nilai' => 85,
            'capaian' => 'Menunjukkan penguasaan materi yang baik.',
        ]);
    }

    /**
     * Test authenticated wali_kelas user can show nilai mapel via JSON.
     */
    public function test_wali_kelas_can_show_nilai_mapel_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $mapel = $this->createMapel($user);
        $nilaiMapel = NilaiMapel::factory()->create([
            'student_id' => $student->id,
            'mapel_id' => $mapel->id,
        ]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.nilai-mapels.show', $nilaiMapel));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $nilaiMapel->id,
                'student_id' => $student->id,
                'mapel_id' => $mapel->id,
            ]);
    }

    /**
     * Test authenticated wali_kelas user can update nilai mapel via JSON.
     */
    public function test_wali_kelas_can_update_nilai_mapel_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $mapel = $this->createMapel($user);
        $nilaiMapel = NilaiMapel::factory()->create([
            'student_id' => $student->id,
            'mapel_id' => $mapel->id,
            'nilai' => 70,
            'capaian' => 'Cukup',
        ]);

        $payload = [
            'student_id' => $student->id,
            'mapel_id' => $mapel->id,
            'nilai' => 90,
            'capaian' => 'Sangat Baik',
        ];

        $response = $this->actingAs($user)->putJson(route('wali-kelas.nilai-mapels.update', $nilaiMapel), $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Nilai mapel updated successfully.',
            ]);

        $this->assertDatabaseHas('nilai_mapels', [
            'id' => $nilaiMapel->id,
            'nilai' => 90,
            'capaian' => 'Sangat Baik',
        ]);
    }

    /**
     * Test authenticated wali_kelas user can destroy nilai mapel via JSON.
     */
    public function test_wali_kelas_can_destroy_nilai_mapel_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $mapel = $this->createMapel($user);
        $nilaiMapel = NilaiMapel::factory()->create([
            'student_id' => $student->id,
            'mapel_id' => $mapel->id,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.nilai-mapels.destroy', $nilaiMapel));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Nilai mapel deleted successfully.',
            ]);

        $this->assertDatabaseMissing('nilai_mapels', [
            'id' => $nilaiMapel->id,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can delete nilai mapel via explicit delete route.
     */
    public function test_wali_kelas_can_delete_nilai_mapel_alias_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $mapel = $this->createMapel($user);
        $nilaiMapel = NilaiMapel::factory()->create([
            'student_id' => $student->id,
            'mapel_id' => $mapel->id,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.nilai-mapels.delete', $nilaiMapel));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Nilai mapel deleted successfully.',
            ]);

        $this->assertDatabaseMissing('nilai_mapels', [
            'id' => $nilaiMapel->id,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can batch store nilai mapels via JSON.
     */
    public function test_wali_kelas_can_batch_store_nilai_mapels_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student1 = $this->createStudent($user);
        $student2 = $this->createStudent($user);
        $mapel = $this->createMapel($user);

        $payload = [
            'mapel_id' => $mapel->id,
            'scores' => [
                [
                    'student_id' => $student1->id,
                    'nilai' => 88,
                    'capaian' => 'Sangat Memuaskan',
                ],
                [
                    'student_id' => $student2->id,
                    'nilai' => 75,
                    'capaian' => 'Cukup Baik',
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson(route('wali-kelas.nilai-mapels.batch'), $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Data nilai mapel berhasil disimpan secara kolektif.',
            ]);

        $this->assertDatabaseHas('nilai_mapels', [
            'student_id' => $student1->id,
            'mapel_id' => $mapel->id,
            'nilai' => 88,
            'capaian' => 'Sangat Memuaskan',
        ]);

        $this->assertDatabaseHas('nilai_mapels', [
            'student_id' => $student2->id,
            'mapel_id' => $mapel->id,
            'nilai' => 75,
            'capaian' => 'Cukup Baik',
        ]);
    }
}
