<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\StudentWaliKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentWaliKelasTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests cannot access student wali kelas routes.
     */
    public function test_guest_cannot_access_student_wali_kelas(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);
        $student = StudentWaliKelas::factory()->create(['class_id' => $classWaliKelas->id]);

        $this->getJson(route('wali-kelas.student-wali-kelas.index'))->assertStatus(401);
        $this->getJson(route('wali-kelas.student-wali-kelas.show', $student))->assertStatus(401);
        $this->postJson(route('wali-kelas.student-wali-kelas.store'), [])->assertStatus(401);
        $this->putJson(route('wali-kelas.student-wali-kelas.update', $student), [])->assertStatus(401);
        $this->deleteJson(route('wali-kelas.student-wali-kelas.destroy', $student))->assertStatus(401);
        $this->deleteJson(route('wali-kelas.student-wali-kelas.delete', $student))->assertStatus(401);
    }

    /**
     * Test non-wali_kelas user cannot access student wali kelas routes.
     */
    public function test_non_wali_kelas_user_cannot_access_student_wali_kelas(): void
    {
        $user = User::factory()->create(['role' => 'mapel']);
        $waliKelasUser = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $waliKelasUser->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $waliKelasUser->id,
        ]);
        $student = StudentWaliKelas::factory()->create(['class_id' => $classWaliKelas->id]);

        $this->actingAs($user)->getJson(route('wali-kelas.student-wali-kelas.index'))->assertStatus(403);
        $this->actingAs($user)->getJson(route('wali-kelas.student-wali-kelas.show', $student))->assertStatus(403);
        $this->actingAs($user)->postJson(route('wali-kelas.student-wali-kelas.store'), [])->assertStatus(403);
        $this->actingAs($user)->putJson(route('wali-kelas.student-wali-kelas.update', $student), [])->assertStatus(403);
        $this->actingAs($user)->deleteJson(route('wali-kelas.student-wali-kelas.destroy', $student))->assertStatus(403);
        $this->actingAs($user)->deleteJson(route('wali-kelas.student-wali-kelas.delete', $student))->assertStatus(403);
    }

    /**
     * Test authenticated wali_kelas user can access student wali kelas index JSON.
     */
    public function test_wali_kelas_can_access_student_wali_kelas_index_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);
        $student = StudentWaliKelas::factory()->create([
            'class_id' => $classWaliKelas->id,
            'name' => 'Budi Santoso',
        ]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.student-wali-kelas.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Budi Santoso',
        ]);
    }

    /**
     * Test authenticated wali_kelas user can store student wali kelas.
     */
    public function test_wali_kelas_can_store_student_wali_kelas(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);

        $payload = [
            'class_id' => $classWaliKelas->id,
            'nis' => '12345678',
            'nisn' => '0012345678',
            'name' => 'Ahmad Subardjo',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'religion' => 'Islam',
            'family_status' => 'Anak Kandung',
            'child_order' => '1',
            'address' => 'Jl. Merdeka No. 17',
            'status' => 'ACTIVE',
        ];

        $response = $this->actingAs($user)->postJson(route('wali-kelas.student-wali-kelas.store'), $payload);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'message' => 'Student wali kelas created successfully.',
            'name' => 'Ahmad Subardjo',
        ]);

        $this->assertDatabaseHas('student_wali_kelas', [
            'nis' => '12345678',
            'name' => 'Ahmad Subardjo',
        ]);
    }

    /**
     * Test storing student wali kelas fails with validation errors when missing required fields.
     */
    public function test_store_student_wali_kelas_validation_error(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);

        $response = $this->actingAs($user)->postJson(route('wali-kelas.student-wali-kelas.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['class_id', 'nis', 'name', 'gender', 'birth_place']);
    }

    /**
     * Test authenticated wali_kelas user can show student wali kelas details.
     */
    public function test_wali_kelas_can_show_student_wali_kelas(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);
        $student = StudentWaliKelas::factory()->create([
            'class_id' => $classWaliKelas->id,
            'name' => 'Siti Nurhaliza',
        ]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.student-wali-kelas.show', $student));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $student->id,
            'name' => 'Siti Nurhaliza',
        ]);
    }

    /**
     * Test authenticated wali_kelas user can update student wali kelas.
     */
    public function test_wali_kelas_can_update_student_wali_kelas(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);
        $student = StudentWaliKelas::factory()->create([
            'class_id' => $classWaliKelas->id,
            'nis' => '111111',
            'name' => 'Nama Lama',
        ]);

        $payload = [
            'class_id' => $classWaliKelas->id,
            'nis' => '111111',
            'name' => 'Nama Baru',
            'gender' => $student->gender,
            'birth_place' => $student->birth_place,
            'birth_date' => '2010-05-05',
            'religion' => $student->religion,
            'family_status' => $student->family_status,
            'child_order' => $student->child_order,
            'address' => $student->address,
            'status' => 'ACTIVE',
        ];

        $response = $this->actingAs($user)->putJson(route('wali-kelas.student-wali-kelas.update', $student), $payload);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Student wali kelas updated successfully.',
            'name' => 'Nama Baru',
        ]);

        $this->assertDatabaseHas('student_wali_kelas', [
            'id' => $student->id,
            'name' => 'Nama Baru',
        ]);
    }

    /**
     * Test authenticated wali_kelas user can destroy student wali kelas.
     */
    public function test_wali_kelas_can_destroy_student_wali_kelas(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);
        $student = StudentWaliKelas::factory()->create(['class_id' => $classWaliKelas->id]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.student-wali-kelas.destroy', $student));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Student wali kelas deleted successfully.',
        ]);

        $this->assertDatabaseMissing('student_wali_kelas', [
            'id' => $student->id,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can delete student wali kelas via custom delete route.
     */
    public function test_wali_kelas_can_delete_student_wali_kelas_via_custom_delete_route(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);
        $student = StudentWaliKelas::factory()->create(['class_id' => $classWaliKelas->id]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.student-wali-kelas.delete', $student));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Student wali kelas deleted successfully.',
        ]);

        $this->assertDatabaseMissing('student_wali_kelas', [
            'id' => $student->id,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can import multiple students via import endpoint.
     */
    public function test_wali_kelas_can_import_students(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);

        $payload = [
            'class_id' => $classWaliKelas->id,
            'students' => [
                [
                    'nis' => '888001',
                    'nisn' => '00888001',
                    'name' => 'Siswa Import Satu',
                    'gender' => 'L',
                    'birth_place' => 'Bandung',
                    'birth_date' => '2011-03-10',
                    'religion' => 'Islam',
                    'family_status' => 'Anak Kandung',
                    'child_order' => '1',
                    'address' => 'Jl. Asia Afrika No. 1',
                    'status' => 'ACTIVE',
                ],
                [
                    'nis' => '888002',
                    'nisn' => '00888002',
                    'name' => 'Siswa Import Dua',
                    'gender' => 'P',
                    'birth_place' => 'Surabaya',
                    'birth_date' => '2011-07-20',
                    'religion' => 'Islam',
                    'family_status' => 'Anak Kandung',
                    'child_order' => '2',
                    'address' => 'Jl. Pemuda No. 10',
                    'status' => 'ACTIVE',
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson(route('wali-kelas.student-wali-kelas.import'), $payload);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => '2 data siswa berhasil diimport.',
        ]);

        $this->assertDatabaseHas('student_wali_kelas', [
            'class_id' => $classWaliKelas->id,
            'nis' => '888001',
            'name' => 'Siswa Import Satu',
        ]);

        $this->assertDatabaseHas('student_wali_kelas', [
            'class_id' => $classWaliKelas->id,
            'nis' => '888002',
            'name' => 'Siswa Import Dua',
        ]);
    }

    /**
     * Test import fails when NIS is duplicate or required fields are missing.
     */
    public function test_import_student_wali_kelas_validation_error(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);

        StudentWaliKelas::factory()->create([
            'class_id' => $classWaliKelas->id,
            'nis' => 'EXISTING_NIS',
        ]);

        $payload = [
            'class_id' => $classWaliKelas->id,
            'students' => [
                [
                    'nis' => 'EXISTING_NIS',
                    'name' => 'Duplikat NIS',
                    'gender' => 'L',
                    'birth_place' => 'Jakarta',
                    'birth_date' => '2010-01-01',
                    'address' => 'Alamat',
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson(route('wali-kelas.student-wali-kelas.import'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['students.0.nis']);
    }

    /**
     * Test importing student wali kelas with nullable birth_date.
     */
    public function test_import_student_wali_kelas_with_nullable_birth_date(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $classWaliKelas = ClassWaliKelas::factory()->create(['user_id' => $user->id]);

        $payload = [
            'class_id' => $classWaliKelas->id,
            'students' => [
                [
                    'nis' => '999001',
                    'name' => 'Siswa Tanpa Tgl Lahir',
                    'gender' => 'L',
                    'birth_place' => 'Bandung',
                    'birth_date' => null,
                    'address' => 'Jl. Kebon Sirih',
                    'status' => 'ACTIVE',
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson(route('wali-kelas.student-wali-kelas.import'), $payload);

        $response->assertStatus(200);
        $this->assertDatabaseHas('student_wali_kelas', [
            'nis' => '999001',
            'birth_date' => null,
        ]);
    }
}
