<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Recaps;
use App\Models\Students;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecapsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_recaps(): void
    {
        $recap = Recaps::factory()->create();

        $this->get(route('recaps.index'))->assertRedirect(route('login'));
        $this->get(route('recaps.show', $recap))->assertRedirect(route('login'));
        $this->post(route('recaps.store'), [])->assertRedirect(route('login'));
        $this->put(route('recaps.update', $recap), [])->assertRedirect(route('login'));
        $this->delete(route('recaps.destroy', $recap))->assertRedirect(route('login'));
        $this->delete(route('recaps.delete', $recap))->assertRedirect(route('login'));
    }

    /**
     * Test user without mapel role cannot access recaps.
     */
    public function test_non_mapel_user_cannot_access_recaps(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'wali_kelas']);

        $response = $this->actingAs($user)->getJson(route('recaps.index'));
        $response->assertStatus(403);
    }

    /**
     * Test authenticated mapel user can access index.
     */
    public function test_authenticated_mapel_user_can_access_recaps_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        Recaps::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('recaps.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Test authenticated mapel user can store recap.
     */
    public function test_authenticated_mapel_user_can_store_recap(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        $academicYear = AcademicYear::factory()->create();
        $class = Classes::factory()->create();
        $student = Students::factory()->create();

        $recapData = [
            'academic_year_id' => $academicYear->id,
            'class_id' => $class->id,
            'student_id' => $student->id,
            'final_score_result' => 88.50,
            'competency_description' => 'Sangat baik dalam memahami materi.',
        ];

        $response = $this->actingAs($user)->postJson(route('recaps.store'), $recapData);

        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Recap created successfully.');
        $response->assertJsonPath('data.final_score_result', '88.50');

        $this->assertDatabaseHas('recaps', [
            'academic_year_id' => $academicYear->id,
            'class_id' => $class->id,
            'student_id' => $student->id,
            'final_score_result' => 88.50,
        ]);
    }

    /**
     * Test validation rules on store.
     */
    public function test_store_recap_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);

        // Test required fields missing
        $response = $this->actingAs($user)->post(route('recaps.store'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'academic_year_id', 'class_id', 'student_id',
        ]);

        // Test non-existing foreign IDs
        $response = $this->actingAs($user)->post(route('recaps.store'), [
            'academic_year_id' => 9999,
            'class_id' => 9999,
            'student_id' => 9999,
            'final_score_result' => 150,
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'academic_year_id', 'class_id', 'student_id', 'final_score_result',
        ]);
    }

    /**
     * Test authenticated mapel user can view details.
     */
    public function test_authenticated_mapel_user_can_view_recap_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        $recap = Recaps::factory()->create();

        $response = $this->actingAs($user)->getJson(route('recaps.show', $recap));

        $response->assertStatus(200);
        $response->assertJsonPath('id', $recap->id);
    }

    /**
     * Test authenticated mapel user can update recap.
     */
    public function test_authenticated_mapel_user_can_update_recap(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        $recap = Recaps::factory()->create(['final_score_result' => 75.00]);

        $newAcademicYear = AcademicYear::factory()->create();
        $newClass = Classes::factory()->create();
        $newStudent = Students::factory()->create();

        $updateData = [
            'academic_year_id' => $newAcademicYear->id,
            'class_id' => $newClass->id,
            'student_id' => $newStudent->id,
            'final_score_result' => 92.00,
            'competency_description' => 'Memiliki pemahaman yang amat baik.',
        ];

        $response = $this->actingAs($user)->putJson(route('recaps.update', $recap), $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Recap updated successfully.');
        $response->assertJsonPath('data.final_score_result', '92.00');

        $this->assertDatabaseHas('recaps', [
            'id' => $recap->id,
            'final_score_result' => 92.00,
        ]);
    }

    /**
     * Test authenticated mapel user can delete recap via destroy.
     */
    public function test_authenticated_mapel_user_can_delete_recap_via_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        $recap = Recaps::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('recaps.destroy', $recap));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Recap deleted successfully.');

        $this->assertDatabaseMissing('recaps', [
            'id' => $recap->id,
        ]);
    }

    /**
     * Test authenticated mapel user can delete recap via delete alias.
     */
    public function test_authenticated_mapel_user_can_delete_recap_via_delete_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        $recap = Recaps::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('recaps.delete', $recap));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Recap deleted successfully.');

        $this->assertDatabaseMissing('recaps', [
            'id' => $recap->id,
        ]);
    }

    /**
     * Test authenticated mapel user can access Nilai Akhir Blade view.
     */
    public function test_authenticated_mapel_user_can_access_nilai_akhir_view(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        $class = Classes::factory()->create();

        $response = $this->actingAs($user)->get(route('nilai-akhir.index', ['class_id' => $class->id]));

        $response->assertStatus(200);
        $response->assertViewIs('auth.nilaiAkhir');
        $response->assertViewHas('selectedClassId', $class->id);
    }

    /**
     * Test authenticated mapel user can batch store recaps.
     */
    public function test_authenticated_mapel_user_can_batch_store_recaps(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        $academicYear = AcademicYear::factory()->create();
        $class = Classes::factory()->create(['academic_year_id' => $academicYear->id]);
        $student1 = Students::factory()->create(['class_id' => $class->id]);
        $student2 = Students::factory()->create(['class_id' => $class->id]);

        $batchData = [
            'class_id' => $class->id,
            'recaps' => [
                [
                    'student_id' => $student1->id,
                    'final_score_result' => 85.00,
                    'competency_description' => 'Sangat baik',
                ],
                [
                    'student_id' => $student2->id,
                    'final_score_result' => 90.00,
                    'competency_description' => 'Luar biasa',
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('recaps.batch'), $batchData);

        $response->assertRedirect(route('nilai-akhir.index', ['class_id' => $class->id]));
        $response->assertSessionHas('success', 'Data Nilai Akhir berhasil disimpan.');

        $this->assertDatabaseHas('recaps', [
            'class_id' => $class->id,
            'student_id' => $student1->id,
            'final_score_result' => 85.00,
            'competency_description' => 'Sangat baik',
        ]);

        $this->assertDatabaseHas('recaps', [
            'class_id' => $class->id,
            'student_id' => $student2->id,
            'final_score_result' => 90.00,
            'competency_description' => 'Luar biasa',
        ]);
    }
}
