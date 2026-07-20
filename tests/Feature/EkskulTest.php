<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\Ekskul;
use App\Models\StudentWaliKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EkskulTest extends TestCase
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
     * Test guests cannot access ekskuls routes.
     */
    public function test_guest_cannot_access_ekskuls(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $ekskul = Ekskul::factory()->create(['student_id' => $student->id]);

        $this->getJson(route('wali-kelas.ekskuls.index'))->assertStatus(401);
        $this->getJson(route('wali-kelas.ekskuls.show', $ekskul))->assertStatus(401);
        $this->postJson(route('wali-kelas.ekskuls.store'), [])->assertStatus(401);
        $this->putJson(route('wali-kelas.ekskuls.update', $ekskul), [])->assertStatus(401);
        $this->deleteJson(route('wali-kelas.ekskuls.destroy', $ekskul))->assertStatus(401);
        $this->deleteJson(route('wali-kelas.ekskuls.delete', $ekskul))->assertStatus(401);
    }

    /**
     * Test non-wali_kelas user cannot access ekskuls routes.
     */
    public function test_non_wali_kelas_user_cannot_access_ekskuls(): void
    {
        $nonWaliKelasUser = User::factory()->create(['role' => 'mapel']);
        $waliKelasUser = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($waliKelasUser);
        $ekskul = Ekskul::factory()->create(['student_id' => $student->id]);

        $this->actingAs($nonWaliKelasUser)->getJson(route('wali-kelas.ekskuls.index'))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->getJson(route('wali-kelas.ekskuls.show', $ekskul))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->postJson(route('wali-kelas.ekskuls.store'), [])->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->putJson(route('wali-kelas.ekskuls.update', $ekskul), [])->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->deleteJson(route('wali-kelas.ekskuls.destroy', $ekskul))->assertStatus(403);
        $this->actingAs($nonWaliKelasUser)->deleteJson(route('wali-kelas.ekskuls.delete', $ekskul))->assertStatus(403);
    }

    /**
     * Test authenticated wali_kelas user can access ekskuls index JSON.
     */
    public function test_wali_kelas_can_access_ekskul_index_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $ekskul = Ekskul::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.ekskuls.index'));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $ekskul->id,
                'student_id' => $student->id,
            ]);
    }

    /**
     * Test authenticated wali_kelas user can filter ekskuls by student_id.
     */
    public function test_wali_kelas_can_filter_ekskuls_by_student_id(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student1 = $this->createStudent($user);
        $student2 = $this->createStudent($user);

        $ekskul1 = Ekskul::factory()->create(['student_id' => $student1->id, 'ekskul1' => 'Pramuka']);
        $ekskul2 = Ekskul::factory()->create(['student_id' => $student2->id, 'ekskul1' => 'Basket']);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.ekskuls.index', ['student_id' => $student1->id]));

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $ekskul1->id])
            ->assertJsonMissing(['id' => $ekskul2->id]);
    }

    /**
     * Test authenticated wali_kelas user can store ekskul via JSON.
     */
    public function test_wali_kelas_can_store_ekskul_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);

        $payload = [
            'student_id' => $student->id,
            'ekskul1' => 'Pramuka',
            'ekskul2' => 'Paskibra',
            'ekskul3' => 'PMR',
        ];

        $response = $this->actingAs($user)->postJson(route('wali-kelas.ekskuls.store'), $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Ekskul created successfully.',
            ])
            ->assertJsonPath('data.ekskul1', 'Pramuka');

        $this->assertDatabaseHas('ekskuls', [
            'student_id' => $student->id,
            'ekskul1' => 'Pramuka',
            'ekskul2' => 'Paskibra',
            'ekskul3' => 'PMR',
        ]);
    }

    /**
     * Test authenticated wali_kelas user can show ekskul JSON.
     */
    public function test_wali_kelas_can_show_ekskul_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $ekskul = Ekskul::factory()->create([
            'student_id' => $student->id,
            'ekskul1' => 'Pramuka',
        ]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.ekskuls.show', $ekskul));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $ekskul->id,
                'ekskul1' => 'Pramuka',
            ]);
    }

    /**
     * Test authenticated wali_kelas user can update ekskul JSON.
     */
    public function test_wali_kelas_can_update_ekskul_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $ekskul = Ekskul::factory()->create([
            'student_id' => $student->id,
            'ekskul1' => 'Pramuka',
        ]);

        $payload = [
            'student_id' => $student->id,
            'ekskul1' => 'Futsal',
            'ekskul2' => 'Basket',
            'ekskul3' => null,
        ];

        $response = $this->actingAs($user)->putJson(route('wali-kelas.ekskuls.update', $ekskul), $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Ekskul updated successfully.',
            ])
            ->assertJsonPath('data.ekskul1', 'Futsal');

        $this->assertDatabaseHas('ekskuls', [
            'id' => $ekskul->id,
            'ekskul1' => 'Futsal',
            'ekskul2' => 'Basket',
        ]);
    }

    /**
     * Test authenticated wali_kelas user can destroy ekskul JSON.
     */
    public function test_wali_kelas_can_destroy_ekskul_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $ekskul = Ekskul::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.ekskuls.destroy', $ekskul));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Ekskul deleted successfully.',
            ]);

        $this->assertDatabaseMissing('ekskuls', [
            'id' => $ekskul->id,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can delete ekskul via delete alias.
     */
    public function test_wali_kelas_can_delete_ekskul_via_alias(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        $ekskul = Ekskul::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.ekskuls.delete', $ekskul));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Ekskul deleted successfully.',
            ]);

        $this->assertDatabaseMissing('ekskuls', [
            'id' => $ekskul->id,
        ]);
    }

    /**
     * Test ekskuls validation errors.
     */
    public function test_ekskuls_validation_errors(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);

        $response = $this->actingAs($user)->postJson(route('wali-kelas.ekskuls.store'), [
            'student_id' => 999999, // Non-existent student ID
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['student_id']);
    }

    /**
     * Test authenticated wali_kelas user can render ekskul blade view via web route.
     */
    public function test_wali_kelas_can_render_ekskul_blade_view(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);
        Ekskul::factory()->create(['student_id' => $student->id, 'ekskul1' => 'Pramuka']);

        $response = $this->actingAs($user)->get(route('wali-kelas.ekstrakurikuler'));

        $response->assertStatus(200)
            ->assertViewIs('auth.waliKelas.ekskul')
            ->assertViewHas('students')
            ->assertSee('Ekstrakurikuler Siswa')
            ->assertSee('Pramuka');
    }

    /**
     * Test authenticated wali_kelas user can store ekskul via web form submission.
     */
    public function test_wali_kelas_can_store_ekskul_via_web_form(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $student = $this->createStudent($user);

        $payload = [
            'student_id' => $student->id,
            'ekskul1' => 'Basket',
            'ekskul2' => 'PMR',
            'ekskul3' => null,
        ];

        $response = $this->actingAs($user)->post(route('wali-kelas.ekskuls.store'), $payload);

        $response->assertStatus(302)
            ->assertSessionHas('success', 'Data ekstrakurikuler berhasil disimpan.');

        $this->assertDatabaseHas('ekskuls', [
            'student_id' => $student->id,
            'ekskul1' => 'Basket',
            'ekskul2' => 'PMR',
        ]);
    }
}
