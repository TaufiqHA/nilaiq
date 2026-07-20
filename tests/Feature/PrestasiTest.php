<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\Prestasi;
use App\Models\StudentWaliKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrestasiTest extends TestCase
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
     * Test guests cannot access prestasis routes.
     */
    public function test_guest_cannot_access_prestasis(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $prestasi = Prestasi::factory()->create(['student_id' => $student->id]);

        $this->getJson(route('wali-kelas.prestasis.index'))->assertStatus(401);
        $this->getJson(route('wali-kelas.prestasis.show', $prestasi))->assertStatus(401);
        $this->postJson(route('wali-kelas.prestasis.store'), [])->assertStatus(401);
        $this->putJson(route('wali-kelas.prestasis.update', $prestasi), [])->assertStatus(401);
        $this->deleteJson(route('wali-kelas.prestasis.destroy', $prestasi))->assertStatus(401);
        $this->deleteJson(route('wali-kelas.prestasis.delete', $prestasi))->assertStatus(401);
    }

    /**
     * Test non-wali_kelas user cannot access prestasis routes.
     */
    public function test_non_wali_kelas_user_cannot_access_prestasis(): void
    {
        $nonWaliKelasUser = User::factory()->create(['role' => 'mapel']);
        $waliKelasUser = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($waliKelasUser);
        $prestasi = Prestasi::factory()->create(['student_id' => $student->id]);

        $this->actingAs($nonWaliKelasUser)->getJson(route('wali-kelas.prestasis.index'))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->getJson(route('wali-kelas.prestasis.show', $prestasi))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->postJson(route('wali-kelas.prestasis.store'), [])->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->putJson(route('wali-kelas.prestasis.update', $prestasi), [])->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->deleteJson(route('wali-kelas.prestasis.destroy', $prestasi))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->deleteJson(route('wali-kelas.prestasis.delete', $prestasi))->assertStatus(403);
    }

    /**
     * Test authenticated wali_kelas user can access prestasis index JSON.
     */
    public function test_wali_kelas_can_access_prestasi_index_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $prestasi = Prestasi::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.prestasis.index'));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $prestasi->id,
                'student_id' => $student->id,
            ]);
    }

    /**
     * Test authenticated wali_kelas user can filter prestasis by student_id.
     */
    public function test_wali_kelas_can_filter_prestasis_by_student_id(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student1 = $this->createStudent($user);
        $student2 = $this->createStudent($user);

        $prestasi1 = Prestasi::factory()->create(['student_id' => $student1->id, 'prestasi1' => 1, 'catatan_prestasi1' => 'Juara 1']);
        $prestasi2 = Prestasi::factory()->create(['student_id' => $student2->id, 'prestasi1' => 2, 'catatan_prestasi1' => 'Juara 2']);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.prestasis.index', ['student_id' => $student1->id]));

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $prestasi1->id])
            ->assertJsonMissing(['id' => $prestasi2->id]);
    }

    /**
     * Test authenticated wali_kelas user can store prestasi via JSON.
     */
    public function test_wali_kelas_can_store_prestasi_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);

        $payload = [
            'student_id' => $student->id,
            'prestasi1' => 1,
            'catatan_prestasi1' => 'Juara 1 O2SN tingkat Kabupaten',
            'prestasi2' => 2,
            'catatan_prestasi2' => 'Juara 2 OSN IPA',
            'prestasi3' => null,
        ];

        $response = $this->actingAs($user)->postJson(route('wali-kelas.prestasis.store'), $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Prestasi created successfully.',
            ])
            ->assertJsonPath('data.catatan_prestasi1', 'Juara 1 O2SN tingkat Kabupaten');

        $this->assertDatabaseHas('prestasis', [
            'student_id' => $student->id,
            'prestasi1' => 1,
            'catatan_prestasi1' => 'Juara 1 O2SN tingkat Kabupaten',
            'prestasi2' => 2,
            'catatan_prestasi2' => 'Juara 2 OSN IPA',
        ]);
    }

    /**
     * Test authenticated wali_kelas user can show prestasi JSON.
     */
    public function test_wali_kelas_can_show_prestasi_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $prestasi = Prestasi::factory()->create([
            'student_id' => $student->id,
            'prestasi1' => 1,
            'catatan_prestasi1' => 'Juara 1 Catur',
        ]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.prestasis.show', $prestasi));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $prestasi->id,
                'catatan_prestasi1' => 'Juara 1 Catur',
            ]);
    }

    /**
     * Test authenticated wali_kelas user can update prestasi JSON.
     */
    public function test_wali_kelas_can_update_prestasi_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $prestasi = Prestasi::factory()->create([
            'student_id' => $student->id,
            'prestasi1' => 1,
            'catatan_prestasi1' => 'Juara 1 Catur',
        ]);

        $payload = [
            'student_id' => $student->id,
            'prestasi1' => 1,
            'catatan_prestasi1' => 'Juara 1 Catur Nasional',
            'prestasi2' => null,
            'prestasi3' => null,
        ];

        $response = $this->actingAs($user)->putJson(route('wali-kelas.prestasis.update', $prestasi), $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Prestasi updated successfully.',
            ])
            ->assertJsonPath('data.catatan_prestasi1', 'Juara 1 Catur Nasional');

        $this->assertDatabaseHas('prestasis', [
            'id' => $prestasi->id,
            'catatan_prestasi1' => 'Juara 1 Catur Nasional',
        ]);
    }

    /**
     * Test authenticated wali_kelas user can destroy prestasi JSON.
     */
    public function test_wali_kelas_can_destroy_prestasi_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $prestasi = Prestasi::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.prestasis.destroy', $prestasi));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Prestasi deleted successfully.',
            ]);

        $this->assertDatabaseMissing('prestasis', [
            'id' => $prestasi->id,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can delete prestasi via delete alias.
     */
    public function test_wali_kelas_can_delete_prestasi_via_alias(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $prestasi = Prestasi::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.prestasis.delete', $prestasi));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Prestasi deleted successfully.',
            ]);

        $this->assertDatabaseMissing('prestasis', [
            'id' => $prestasi->id,
        ]);
    }

    /**
     * Test prestasis validation errors.
     */
    public function test_prestasis_validation_errors(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);

        $response = $this->actingAs($user)->postJson(route('wali-kelas.prestasis.store'), [
            'student_id' => 999999, // Non-existent student ID
            'prestasi1' => 'bukan_integer',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['student_id', 'prestasi1']);
    }
}
