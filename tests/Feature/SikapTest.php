<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\Sikap;
use App\Models\StudentWaliKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SikapTest extends TestCase
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
     * Test guests cannot access sikaps routes.
     */
    public function test_guest_cannot_access_sikaps(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $sikap = Sikap::factory()->create(['student_id' => $student->id]);

        $this->getJson(route('wali-kelas.sikaps.index'))->assertStatus(401);
        $this->getJson(route('wali-kelas.sikaps.show', $sikap))->assertStatus(401);
        $this->postJson(route('wali-kelas.sikaps.store'), [])->assertStatus(401);
        $this->putJson(route('wali-kelas.sikaps.update', $sikap), [])->assertStatus(401);
        $this->deleteJson(route('wali-kelas.sikaps.destroy', $sikap))->assertStatus(401);
        $this->deleteJson(route('wali-kelas.sikaps.delete', $sikap))->assertStatus(401);
    }

    /**
     * Test non-wali_kelas user cannot access sikaps routes.
     */
    public function test_non_wali_kelas_user_cannot_access_sikaps(): void
    {
        $nonWaliKelasUser = User::factory()->create(['role' => 'mapel']);
        $waliKelasUser = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($waliKelasUser);
        $sikap = Sikap::factory()->create(['student_id' => $student->id]);

        $this->actingAs($nonWaliKelasUser)->getJson(route('wali-kelas.sikaps.index'))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->getJson(route('wali-kelas.sikaps.show', $sikap))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->postJson(route('wali-kelas.sikaps.store'), [])->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->putJson(route('wali-kelas.sikaps.update', $sikap), [])->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->deleteJson(route('wali-kelas.sikaps.destroy', $sikap))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->deleteJson(route('wali-kelas.sikaps.delete', $sikap))->assertStatus(403);
    }

    /**
     * Test authenticated wali_kelas user can access sikaps index JSON.
     */
    public function test_wali_kelas_can_access_sikap_index_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $sikap = Sikap::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.sikaps.index'));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $sikap->id,
                'student_id' => $student->id,
            ]);
    }

    /**
     * Test authenticated wali_kelas user can filter sikaps by student_id.
     */
    public function test_wali_kelas_can_filter_sikaps_by_student_id(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student1 = $this->createStudent($user);
        $student2 = $this->createStudent($user);

        $sikap1 = Sikap::factory()->create(['student_id' => $student1->id, 'mandiri' => 'Sangat Mandiri']);
        $sikap2 = Sikap::factory()->create(['student_id' => $student2->id, 'mandiri' => 'Cukup Mandiri']);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.sikaps.index', ['student_id' => $student1->id]));

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $sikap1->id])
            ->assertJsonMissing(['id' => $sikap2->id]);
    }

    /**
     * Test authenticated wali_kelas user can store sikap via JSON.
     */
    public function test_wali_kelas_can_store_sikap_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);

        $payload = [
            'student_id' => $student->id,
            'beriman_bertakwa_dan_berakhlak_mulia' => 'Sangat baik',
            'mandiri' => 'Mandiri',
            'bergotong_royong' => 'Aktif bergotong royong',
            'kreatif' => 'Sangat kreatif',
            'bernalar_kritis' => 'Bernalar kritis',
            'berkebinekaan_global' => 'Memiliki sikap toleransi tinggi',
        ];

        $response = $this->actingAs($user)->postJson(route('wali-kelas.sikaps.store'), $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Sikap created successfully.',
                'student_id' => $student->id,
            ]);

        $this->assertDatabaseHas('sikaps', [
            'student_id' => $student->id,
            'mandiri' => 'Mandiri',
        ]);
    }

    /**
     * Test authenticated wali_kelas user can show sikap via JSON.
     */
    public function test_wali_kelas_can_show_sikap_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $sikap = Sikap::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.sikaps.show', $sikap));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $sikap->id,
                'student_id' => $student->id,
            ]);
    }

    /**
     * Test authenticated wali_kelas user can update sikap via JSON.
     */
    public function test_wali_kelas_can_update_sikap_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $sikap = Sikap::factory()->create([
            'student_id' => $student->id,
            'mandiri' => 'Kurang Mandiri',
        ]);

        $payload = [
            'student_id' => $student->id,
            'mandiri' => 'Sangat Mandiri dan Disiplin',
        ];

        $response = $this->actingAs($user)->putJson(route('wali-kelas.sikaps.update', $sikap), $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Sikap updated successfully.',
            ]);

        $this->assertDatabaseHas('sikaps', [
            'id' => $sikap->id,
            'mandiri' => 'Sangat Mandiri dan Disiplin',
        ]);
    }

    /**
     * Test authenticated wali_kelas user can destroy sikap via JSON.
     */
    public function test_wali_kelas_can_destroy_sikap_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $sikap = Sikap::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.sikaps.destroy', $sikap));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Sikap deleted successfully.',
            ]);

        $this->assertDatabaseMissing('sikaps', [
            'id' => $sikap->id,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can delete sikap via explicit delete route.
     */
    public function test_wali_kelas_can_delete_sikap_alias_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $sikap = Sikap::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.sikaps.delete', $sikap));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Sikap deleted successfully.',
            ]);

        $this->assertDatabaseMissing('sikaps', [
            'id' => $sikap->id,
        ]);
    }
}
