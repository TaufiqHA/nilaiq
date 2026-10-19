<?php

namespace Tests\Feature\Api;

use App\Models\Classes;
use App\Models\FinalExams;
use App\Models\FinalScores;
use App\Models\MidtermExams;
use App\Models\MidtermScores;
use App\Models\Students;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiMidtermFinalTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsMapel(): User
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        Sanctum::actingAs($user);

        return $user;
    }

    /**
     * Guests cannot access the PTS/PAS endpoints.
     */
    public function test_guest_cannot_access_exam_endpoints(): void
    {
        $this->getJson('/api/midterm-exams')->assertStatus(401);
        $this->postJson('/api/midterm-exams', [])->assertStatus(401);
        $this->getJson('/api/midterm-scores')->assertStatus(401);
        $this->postJson('/api/midterm-scores', [])->assertStatus(401);
        $this->getJson('/api/final-exams')->assertStatus(401);
        $this->postJson('/api/final-exams', [])->assertStatus(401);
        $this->getJson('/api/final-scores')->assertStatus(401);
        $this->postJson('/api/final-scores', [])->assertStatus(401);
    }

    /**
     * The PTS/PAS index includes class students and scores.
     */
    public function test_exam_index_returns_relations(): void
    {
        $this->actingAsMapel();
        MidtermExams::factory()->create();
        FinalExams::factory()->create();

        $this->getJson('/api/midterm-exams')
            ->assertStatus(200)
            ->assertJsonStructure([
                ['id', 'title', 'exam_date', 'class' => ['id', 'name', 'students'], 'scores'],
            ]);

        $this->getJson('/api/final-exams')
            ->assertStatus(200)
            ->assertJsonStructure([
                ['id', 'title', 'exam_date', 'class' => ['id', 'name', 'students'], 'scores'],
            ]);
    }

    /**
     * Storing PTS and PAS exams persists them.
     */
    public function test_can_store_midterm_and_final_exams(): void
    {
        $this->actingAsMapel();
        $class = Classes::factory()->create();

        $this->postJson('/api/midterm-exams', [
            'class_id' => $class->id,
            'title' => 'PTS Ganjil Matematika',
            'exam_date' => '2026-09-15',
            'description' => 'Materi bab 1-3.',
        ])->assertStatus(201)->assertJsonPath('data.title', 'PTS Ganjil Matematika');

        $this->postJson('/api/final-exams', [
            'class_id' => $class->id,
            'title' => 'PAS Ganjil Matematika',
            'exam_date' => '2026-12-01',
            'description' => 'Materi semester ganjil.',
        ])->assertStatus(201)->assertJsonPath('data.title', 'PAS Ganjil Matematika');

        $this->assertDatabaseHas('midterm_exams', ['title' => 'PTS Ganjil Matematika']);
        $this->assertDatabaseHas('final_exams', ['title' => 'PAS Ganjil Matematika']);
    }

    /**
     * Storing with the same client_uuid is idempotent for both exam types.
     */
    public function test_storing_exams_is_idempotent_with_client_uuid(): void
    {
        $this->actingAsMapel();
        $class = Classes::factory()->create();

        $midtermPayload = [
            'client_uuid' => '33333333-3333-4333-8333-333333333333',
            'class_id' => $class->id,
            'title' => 'PTS Offline',
            'exam_date' => '2026-09-15',
            'description' => 'Dibuat offline.',
        ];
        $finalPayload = [
            'client_uuid' => '44444444-4444-4444-8444-444444444444',
            'class_id' => $class->id,
            'title' => 'PAS Offline',
            'exam_date' => '2026-12-01',
            'description' => 'Dibuat offline.',
        ];

        $this->postJson('/api/midterm-exams', $midtermPayload)->assertStatus(201);
        $this->postJson('/api/midterm-exams', $midtermPayload)->assertStatus(200);
        $this->postJson('/api/final-exams', $finalPayload)->assertStatus(201);
        $this->postJson('/api/final-exams', $finalPayload)->assertStatus(200);

        $this->assertDatabaseCount('midterm_exams', 1);
        $this->assertDatabaseCount('final_exams', 1);
    }

    /**
     * Exams can be updated and deleted.
     */
    public function test_can_update_and_delete_exams(): void
    {
        $this->actingAsMapel();
        $midterm = MidtermExams::factory()->create();
        $final = FinalExams::factory()->create();
        $class = Classes::factory()->create();

        $this->putJson('/api/midterm-exams/'.$midterm->id, [
            'class_id' => $class->id,
            'title' => 'PTS Baru',
            'exam_date' => '2026-09-16',
            'description' => 'Deskripsi baru.',
        ])->assertStatus(200)->assertJsonPath('data.title', 'PTS Baru');

        $this->putJson('/api/final-exams/'.$final->id, [
            'class_id' => $class->id,
            'title' => 'PAS Baru',
            'exam_date' => '2026-12-02',
            'description' => 'Deskripsi baru.',
        ])->assertStatus(200)->assertJsonPath('data.title', 'PAS Baru');

        $this->deleteJson('/api/midterm-exams/'.$midterm->id)->assertStatus(200);
        $this->deleteJson('/api/final-exams/'.$final->id)->assertStatus(200);

        $this->assertDatabaseMissing('midterm_exams', ['id' => $midterm->id]);
        $this->assertDatabaseMissing('final_exams', ['id' => $final->id]);
    }

    /**
     * Storing PTS and PAS scores creates records.
     */
    public function test_can_store_midterm_and_final_scores(): void
    {
        $this->actingAsMapel();
        $midterm = MidtermExams::factory()->create();
        $final = FinalExams::factory()->create();
        $student = Students::factory()->create();

        $this->postJson('/api/midterm-scores', [
            'midterm_exam_id' => $midterm->id,
            'student_id' => $student->id,
            'score' => 88.5,
        ])->assertStatus(201);

        $this->postJson('/api/final-scores', [
            'final_exam_id' => $final->id,
            'student_id' => $student->id,
            'score' => 91,
        ])->assertStatus(201);

        $this->assertDatabaseHas('midterm_scores', [
            'midterm_exam_id' => $midterm->id,
            'student_id' => $student->id,
            'score' => 88.5,
        ]);
        $this->assertDatabaseHas('final_scores', [
            'final_exam_id' => $final->id,
            'student_id' => $student->id,
            'score' => 91,
        ]);
    }

    /**
     * Repeating a score for the same exam+student upserts rather than duplicating.
     */
    public function test_storing_exam_scores_upserts_for_same_student_and_exam(): void
    {
        $this->actingAsMapel();
        $midterm = MidtermExams::factory()->create();
        $final = FinalExams::factory()->create();
        $student = Students::factory()->create();

        $this->postJson('/api/midterm-scores', [
            'midterm_exam_id' => $midterm->id,
            'student_id' => $student->id,
            'score' => 70,
        ])->assertStatus(201);
        $this->postJson('/api/midterm-scores', [
            'midterm_exam_id' => $midterm->id,
            'student_id' => $student->id,
            'score' => 95,
        ])->assertStatus(200);

        $this->postJson('/api/final-scores', [
            'final_exam_id' => $final->id,
            'student_id' => $student->id,
            'score' => 60,
        ])->assertStatus(201);
        $this->postJson('/api/final-scores', [
            'final_exam_id' => $final->id,
            'student_id' => $student->id,
            'score' => 80,
        ])->assertStatus(200);

        $this->assertDatabaseCount('midterm_scores', 1);
        $this->assertDatabaseCount('final_scores', 1);
        $this->assertDatabaseHas('midterm_scores', ['score' => 95]);
        $this->assertDatabaseHas('final_scores', ['score' => 80]);
    }

    /**
     * Exam scores can be updated and deleted.
     */
    public function test_can_update_and_delete_exam_scores(): void
    {
        $this->actingAsMapel();
        $midterm = MidtermExams::factory()->create();
        $final = FinalExams::factory()->create();
        $student = Students::factory()->create();
        $midtermScore = MidtermScores::factory()->create([
            'midterm_exam_id' => $midterm->id,
            'student_id' => $student->id,
        ]);
        $finalScore = FinalScores::factory()->create([
            'final_exam_id' => $final->id,
            'student_id' => $student->id,
        ]);

        $this->putJson('/api/midterm-scores/'.$midtermScore->id, [
            'midterm_exam_id' => $midterm->id,
            'student_id' => $student->id,
            'score' => 60,
        ])->assertStatus(200)->assertJsonPath('data.score', 60);

        $this->putJson('/api/final-scores/'.$finalScore->id, [
            'final_exam_id' => $final->id,
            'student_id' => $student->id,
            'score' => 65,
        ])->assertStatus(200)->assertJsonPath('data.score', 65);

        $this->deleteJson('/api/midterm-scores/'.$midtermScore->id)->assertStatus(200);
        $this->deleteJson('/api/final-scores/'.$finalScore->id)->assertStatus(200);

        $this->assertDatabaseMissing('midterm_scores', ['id' => $midtermScore->id]);
        $this->assertDatabaseMissing('final_scores', ['id' => $finalScore->id]);
    }
}
