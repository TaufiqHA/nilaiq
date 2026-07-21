<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\CatatanWaliKelas;
use App\Models\ClassWaliKelas;
use App\Models\StudentWaliKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatatanWaliKelasTest extends TestCase
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

    /**
     * Test guests cannot access catatan wali kelas routes.
     */
    public function test_guest_cannot_access_catatan_wali_kelas(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $catatanWaliKelas = CatatanWaliKelas::factory()->create(['student_id' => $student->id]);

        $this->getJson(route('wali-kelas.catatan-wali-kelas.index'))->assertStatus(401);
        $this->getJson(route('wali-kelas.catatan-wali-kelas.show', $catatanWaliKelas))->assertStatus(401);
        $this->postJson(route('wali-kelas.catatan-wali-kelas.store'), [])->assertStatus(401);
        $this->putJson(route('wali-kelas.catatan-wali-kelas.update', $catatanWaliKelas), [])->assertStatus(401);
        $this->deleteJson(route('wali-kelas.catatan-wali-kelas.destroy', $catatanWaliKelas))->assertStatus(401);
        $this->deleteJson(route('wali-kelas.catatan-wali-kelas.delete', $catatanWaliKelas))->assertStatus(401);
    }

    /**
     * Test non-wali_kelas user cannot access catatan wali kelas routes.
     */
    public function test_non_wali_kelas_user_cannot_access_catatan_wali_kelas(): void
    {
        $nonWaliKelasUser = User::factory()->create(['role' => 'mapel']);
        $waliKelasUser = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($waliKelasUser);
        $catatanWaliKelas = CatatanWaliKelas::factory()->create(['student_id' => $student->id]);

        $this->actingAs($nonWaliKelasUser)->getJson(route('wali-kelas.catatan-wali-kelas.index'))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->getJson(route('wali-kelas.catatan-wali-kelas.show', $catatanWaliKelas))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->postJson(route('wali-kelas.catatan-wali-kelas.store'), [])->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->putJson(route('wali-kelas.catatan-wali-kelas.update', $catatanWaliKelas), [])->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->deleteJson(route('wali-kelas.catatan-wali-kelas.destroy', $catatanWaliKelas))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->deleteJson(route('wali-kelas.catatan-wali-kelas.delete', $catatanWaliKelas))->assertStatus(403);
    }

    /**
     * Test authenticated wali_kelas user can access catatan wali kelas index JSON.
     */
    public function test_wali_kelas_can_access_catatan_wali_kelas_index_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $catatanWaliKelas = CatatanWaliKelas::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.catatan-wali-kelas.index'));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $catatanWaliKelas->id,
                'student_id' => $student->id,
            ]);
    }

    /**
     * Test authenticated wali_kelas user can filter catatan wali kelas by student_id.
     */
    public function test_wali_kelas_can_filter_catatan_wali_kelas_by_student_id(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student1 = $this->createStudent($user);
        $student2 = $this->createStudent($user);

        $catatan1 = CatatanWaliKelas::factory()->create(['student_id' => $student1->id, 'catatan' => 'Catatan Siswa 1']);
        $catatan2 = CatatanWaliKelas::factory()->create(['student_id' => $student2->id, 'catatan' => 'Catatan Siswa 2']);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.catatan-wali-kelas.index', ['student_id' => $student1->id]));

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $catatan1->id])
            ->assertJsonMissing(['id' => $catatan2->id]);
    }

    /**
     * Test authenticated wali_kelas user can store catatan wali kelas via JSON.
     */
    public function test_wali_kelas_can_store_catatan_wali_kelas_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);

        $payload = [
            'student_id' => $student->id,
            'catatan' => 'Siswa menunjukkan peningkatan prestasi belajar.',
        ];

        $response = $this->actingAs($user)->postJson(route('wali-kelas.catatan-wali-kelas.store'), $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Catatan wali kelas created successfully.',
                'student_id' => $student->id,
            ]);

        $this->assertDatabaseHas('catatan_wali_kelas', [
            'student_id' => $student->id,
            'catatan' => 'Siswa menunjukkan peningkatan prestasi belajar.',
        ]);
    }

    /**
     * Test authenticated wali_kelas user can show catatan wali kelas via JSON.
     */
    public function test_wali_kelas_can_show_catatan_wali_kelas_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $catatanWaliKelas = CatatanWaliKelas::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.catatan-wali-kelas.show', $catatanWaliKelas));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $catatanWaliKelas->id,
                'student_id' => $student->id,
            ]);
    }

    /**
     * Test authenticated wali_kelas user can update catatan wali kelas via JSON.
     */
    public function test_wali_kelas_can_update_catatan_wali_kelas_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $catatanWaliKelas = CatatanWaliKelas::factory()->create([
            'student_id' => $student->id,
            'catatan' => 'Catatan Lama',
        ]);

        $payload = [
            'student_id' => $student->id,
            'catatan' => 'Catatan Baru yang telah diperbarui',
        ];

        $response = $this->actingAs($user)->putJson(route('wali-kelas.catatan-wali-kelas.update', $catatanWaliKelas), $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Catatan wali kelas updated successfully.',
            ]);

        $this->assertDatabaseHas('catatan_wali_kelas', [
            'id' => $catatanWaliKelas->id,
            'catatan' => 'Catatan Baru yang telah diperbarui',
        ]);
    }

    /**
     * Test authenticated wali_kelas user can destroy catatan wali kelas via JSON.
     */
    public function test_wali_kelas_can_destroy_catatan_wali_kelas_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $catatanWaliKelas = CatatanWaliKelas::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.catatan-wali-kelas.destroy', $catatanWaliKelas));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Catatan wali kelas deleted successfully.',
            ]);

        $this->assertDatabaseMissing('catatan_wali_kelas', [
            'id' => $catatanWaliKelas->id,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can delete catatan wali kelas via explicit delete route.
     */
    public function test_wali_kelas_can_delete_catatan_wali_kelas_alias_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $catatanWaliKelas = CatatanWaliKelas::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.catatan-wali-kelas.delete', $catatanWaliKelas));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Catatan wali kelas deleted successfully.',
            ]);

        $this->assertDatabaseMissing('catatan_wali_kelas', [
            'id' => $catatanWaliKelas->id,
        ]);
    }
}
