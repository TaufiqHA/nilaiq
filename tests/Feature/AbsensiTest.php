<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\StudentWaliKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsensiTest extends TestCase
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
     * Test guests cannot access absensis routes.
     */
    public function test_guest_cannot_access_absensis(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $absensi = Absensi::factory()->create(['student_id' => $student->id]);

        $this->getJson(route('wali-kelas.absensis.index'))->assertStatus(401);
        $this->getJson(route('wali-kelas.absensis.show', $absensi))->assertStatus(401);
        $this->postJson(route('wali-kelas.absensis.store'), [])->assertStatus(401);
        $this->putJson(route('wali-kelas.absensis.update', $absensi), [])->assertStatus(401);
        $this->deleteJson(route('wali-kelas.absensis.destroy', $absensi))->assertStatus(401);
        $this->deleteJson(route('wali-kelas.absensis.delete', $absensi))->assertStatus(401);
    }

    /**
     * Test non-wali_kelas user cannot access absensis routes.
     */
    public function test_non_wali_kelas_user_cannot_access_absensis(): void
    {
        $nonWaliKelasUser = User::factory()->create(['role' => 'mapel']);
        $waliKelasUser = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($waliKelasUser);
        $absensi = Absensi::factory()->create(['student_id' => $student->id]);

        $this->actingAs($nonWaliKelasUser)->getJson(route('wali-kelas.absensis.index'))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->getJson(route('wali-kelas.absensis.show', $absensi))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->postJson(route('wali-kelas.absensis.store'), [])->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->putJson(route('wali-kelas.absensis.update', $absensi), [])->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->deleteJson(route('wali-kelas.absensis.destroy', $absensi))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->deleteJson(route('wali-kelas.absensis.delete', $absensi))->assertStatus(403);
    }

    /**
     * Test authenticated wali_kelas user can access absensis index JSON.
     */
    public function test_wali_kelas_can_access_absensi_index_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $absensi = Absensi::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.absensis.index'));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $absensi->id,
                'student_id' => $student->id,
            ]);
    }

    /**
     * Test authenticated wali_kelas user can access absensi html view.
     */
    public function test_wali_kelas_can_view_absensi_page(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        Absensi::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->get(route('wali-kelas.absensi'));

        $response->assertStatus(200)
            ->assertViewIs('auth.waliKelas.absensi')
            ->assertViewHas('students');
    }

    /**
     * Test authenticated wali_kelas user can filter absensis by student_id.
     */
    public function test_wali_kelas_can_filter_absensis_by_student_id(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student1 = $this->createStudent($user);
        $student2 = $this->createStudent($user);

        $absensi1 = Absensi::factory()->create(['student_id' => $student1->id, 'hadir' => 20]);
        $absensi2 = Absensi::factory()->create(['student_id' => $student2->id, 'hadir' => 15]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.absensis.index', ['student_id' => $student1->id]));

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $absensi1->id])
            ->assertJsonMissing(['id' => $absensi2->id]);
    }

    /**
     * Test authenticated wali_kelas user can store absensi via JSON.
     */
    public function test_wali_kelas_can_store_absensi_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);

        $payload = [
            'student_id' => $student->id,
            'hadir' => 22,
            'izin' => 2,
            'sakit' => 1,
            'alpa' => 0,
        ];

        $response = $this->actingAs($user)->postJson(route('wali-kelas.absensis.store'), $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Absensi created successfully.',
                'student_id' => $student->id,
            ]);

        $this->assertDatabaseHas('absensis', [
            'student_id' => $student->id,
            'hadir' => 22,
            'izin' => 2,
            'sakit' => 1,
            'alpa' => 0,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can show absensi via JSON.
     */
    public function test_wali_kelas_can_show_absensi_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $absensi = Absensi::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.absensis.show', $absensi));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $absensi->id,
                'student_id' => $student->id,
            ]);
    }

    /**
     * Test authenticated wali_kelas user can update absensi via JSON.
     */
    public function test_wali_kelas_can_update_absensi_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $absensi = Absensi::factory()->create([
            'student_id' => $student->id,
            'hadir' => 10,
        ]);

        $payload = [
            'student_id' => $student->id,
            'hadir' => 25,
            'izin' => 1,
            'sakit' => 0,
            'alpa' => 0,
        ];

        $response = $this->actingAs($user)->putJson(route('wali-kelas.absensis.update', $absensi), $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Absensi updated successfully.',
            ]);

        $this->assertDatabaseHas('absensis', [
            'id' => $absensi->id,
            'hadir' => 25,
            'izin' => 1,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can destroy absensi via JSON.
     */
    public function test_wali_kelas_can_destroy_absensi_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $absensi = Absensi::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.absensis.destroy', $absensi));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Absensi deleted successfully.',
            ]);

        $this->assertDatabaseMissing('absensis', [
            'id' => $absensi->id,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can delete absensi via explicit delete route.
     */
    public function test_wali_kelas_can_delete_absensi_alias_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $absensi = Absensi::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.absensis.delete', $absensi));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Absensi deleted successfully.',
            ]);

        $this->assertDatabaseMissing('absensis', [
            'id' => $absensi->id,
        ]);
    }
}
