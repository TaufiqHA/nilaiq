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
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

    /**
     * Test authenticated wali_kelas user can export values template.
     */
    public function test_wali_kelas_can_export_nilai_template(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $mapel = $this->createMapel($user);

        $response = $this->actingAs($user)->get(route('wali-kelas.nilai-mapels.export', [
            'mapel_id' => $mapel->id,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
        $this->assertTrue(str_contains(
            $response->headers->get('Content-Disposition'),
            'attachment;filename="Template_Nilai_'
        ));
    }

    /**
     * Test authenticated wali_kelas user can import grades via Excel.
     */
    public function test_wali_kelas_can_import_nilai_via_excel(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $mapel = $this->createMapel($user);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID Siswa (Jangan Diubah)');
        $sheet->setCellValue('B1', 'NIS');
        $sheet->setCellValue('C1', 'NISN');
        $sheet->setCellValue('D1', 'Nama Siswa');
        $sheet->setCellValue('E1', 'Nilai Akhir (0-100)');
        $sheet->setCellValue('F1', 'Capaian Pembelajaran');

        $sheet->setCellValue('A2', $student->id);
        $sheet->setCellValue('B2', $student->nis);
        $sheet->setCellValue('C2', $student->nisn);
        $sheet->setCellValue('D2', $student->name);
        $sheet->setCellValue('E2', 90);
        $sheet->setCellValue('F2', 'Sangat Baik');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_nilai').'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'template.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($user)->post(route('wali-kelas.nilai-mapels.import'), [
            'mapel_id' => $mapel->id,
            'file' => $uploadedFile,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('nilai_mapels', [
            'student_id' => $student->id,
            'mapel_id' => $mapel->id,
            'nilai' => 90,
            'capaian' => 'Sangat Baik',
        ]);

        @unlink($tempPath);
    }

    /**
     * Test import fails if validation fails (e.g. invalid grades).
     */
    public function test_import_fails_on_invalid_grade_range(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $mapel = $this->createMapel($user);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID Siswa (Jangan Diubah)');
        $sheet->setCellValue('D1', 'Nama Siswa');
        $sheet->setCellValue('E1', 'Nilai Akhir (0-100)');

        $sheet->setCellValue('A2', $student->id);
        $sheet->setCellValue('D2', $student->name);
        $sheet->setCellValue('E2', 120); // Invalid grade range (>100)

        $tempPath = tempnam(sys_get_temp_dir(), 'test_nilai_invalid').'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'template.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($user)->post(route('wali-kelas.nilai-mapels.import'), [
            'mapel_id' => $mapel->id,
            'file' => $uploadedFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('nilai_mapels', [
            'student_id' => $student->id,
            'mapel_id' => $mapel->id,
        ]);

        @unlink($tempPath);
    }

    /**
     * Test import fails if student is outside wali_kelas class.
     */
    public function test_import_fails_for_student_outside_class(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $this->createStudent($user);
        $mapel = $this->createMapel($user);

        // Student outside user's class
        $otherUser = User::factory()->create(['role' => 'wali_kelas']);
        $otherStudent = $this->createStudent($otherUser);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID Siswa (Jangan Diubah)');
        $sheet->setCellValue('D1', 'Nama Siswa');
        $sheet->setCellValue('E1', 'Nilai Akhir (0-100)');

        $sheet->setCellValue('A2', $otherStudent->id);
        $sheet->setCellValue('D2', $otherStudent->name);
        $sheet->setCellValue('E2', 85);

        $tempPath = tempnam(sys_get_temp_dir(), 'test_nilai_outside').'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'template.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($user)->post(route('wali-kelas.nilai-mapels.import'), [
            'mapel_id' => $mapel->id,
            'file' => $uploadedFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
        $this->assertDatabaseMissing('nilai_mapels', [
            'student_id' => $otherStudent->id,
            'mapel_id' => $mapel->id,
        ]);

        @unlink($tempPath);
    }
}
