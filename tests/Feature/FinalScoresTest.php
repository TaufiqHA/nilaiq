<?php

namespace Tests\Feature;

use App\Models\FinalExams;
use App\Models\FinalScores;
use App\Models\Students;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinalScoresTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_final_scores(): void
    {
        $score = FinalScores::factory()->create();

        $this->get(route('final-scores.index'))->assertRedirect(route('login'));
        $this->get(route('final-scores.show', $score))->assertRedirect(route('login'));
        $this->post(route('final-scores.store'), [])->assertRedirect(route('login'));
        $this->put(route('final-scores.update', $score), [])->assertRedirect(route('login'));
        $this->delete(route('final-scores.destroy', $score))->assertRedirect(route('login'));
        $this->delete(route('final-scores.delete', $score))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access index.
     */
    public function test_authenticated_user_can_access_final_scores_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        FinalScores::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('final-scores.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Test authenticated user can store final score.
     */
    public function test_authenticated_user_can_store_final_score(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $exam = FinalExams::factory()->create();
        $student = Students::factory()->create();

        $scoreData = [
            'final_exam_id' => $exam->id,
            'student_id' => $student->id,
            'score' => 85.50,
        ];

        $response = $this->actingAs($user)->postJson(route('final-scores.store'), $scoreData);

        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Final score created successfully.');
        $response->assertJsonPath('data.score', 85.50);

        $this->assertDatabaseHas('final_scores', [
            'final_exam_id' => $exam->id,
            'student_id' => $student->id,
            'score' => 85.50,
        ]);
    }

    /**
     * Test validation rules on store.
     */
    public function test_store_final_score_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Test required fields
        $response = $this->actingAs($user)->post(route('final-scores.store'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'final_exam_id', 'student_id', 'score',
        ]);

        // Test invalid values
        $response = $this->actingAs($user)->post(route('final-scores.store'), [
            'final_exam_id' => 9999,
            'student_id' => 9999,
            'score' => 105,
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'final_exam_id', 'student_id', 'score',
        ]);

        $response2 = $this->actingAs($user)->post(route('final-scores.store'), [
            'final_exam_id' => 9999,
            'student_id' => 9999,
            'score' => -5,
        ]);
        $response2->assertStatus(302);
        $response2->assertSessionHasErrors([
            'score',
        ]);
    }

    /**
     * Test authenticated user can view details.
     */
    public function test_authenticated_user_can_view_final_score_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = FinalScores::factory()->create();

        $response = $this->actingAs($user)->getJson(route('final-scores.show', $score));

        $response->assertStatus(200);
        $response->assertJsonPath('id', $score->id);
        $response->assertJsonPath('score', $score->score);
    }

    /**
     * Test authenticated user can update final score.
     */
    public function test_authenticated_user_can_update_final_score(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = FinalScores::factory()->create([
            'score' => 70.00,
        ]);
        $newExam = FinalExams::factory()->create();
        $newStudent = Students::factory()->create();

        $updateData = [
            'final_exam_id' => $newExam->id,
            'student_id' => $newStudent->id,
            'score' => 90.50,
        ];

        $response = $this->actingAs($user)->putJson(route('final-scores.update', $score), $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Final score updated successfully.');
        $response->assertJsonPath('data.score', 90.50);

        $this->assertDatabaseHas('final_scores', [
            'id' => $score->id,
            'final_exam_id' => $newExam->id,
            'student_id' => $newStudent->id,
            'score' => 90.50,
        ]);
    }

    /**
     * Test authenticated user can destroy final score.
     */
    public function test_authenticated_user_can_delete_final_score_via_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = FinalScores::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('final-scores.destroy', $score));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Final score deleted successfully.');

        $this->assertDatabaseMissing('final_scores', [
            'id' => $score->id,
        ]);
    }

    /**
     * Test authenticated user can delete via delete route.
     */
    public function test_authenticated_user_can_delete_final_score_via_delete_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = FinalScores::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('final-scores.delete', $score));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Final score deleted successfully.');

        $this->assertDatabaseMissing('final_scores', [
            'id' => $score->id,
        ]);
    }
}
