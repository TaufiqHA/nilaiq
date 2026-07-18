<?php

namespace Tests\Feature;

use App\Models\MidtermExams;
use App\Models\MidtermScores;
use App\Models\Students;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtermScoresTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_midterm_scores(): void
    {
        $score = MidtermScores::factory()->create();

        $this->get(route('midterm-scores.index'))->assertRedirect(route('login'));
        $this->get(route('midterm-scores.show', $score))->assertRedirect(route('login'));
        $this->post(route('midterm-scores.store'), [])->assertRedirect(route('login'));
        $this->put(route('midterm-scores.update', $score), [])->assertRedirect(route('login'));
        $this->delete(route('midterm-scores.destroy', $score))->assertRedirect(route('login'));
        $this->delete(route('midterm-scores.delete', $score))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access index.
     */
    public function test_authenticated_user_can_access_midterm_scores_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        MidtermScores::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('midterm-scores.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Test authenticated user can store midterm score.
     */
    public function test_authenticated_user_can_store_midterm_score(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $exam = MidtermExams::factory()->create();
        $student = Students::factory()->create();

        $scoreData = [
            'midterm_exam_id' => $exam->id,
            'student_id' => $student->id,
            'score' => 85.50,
        ];

        $response = $this->actingAs($user)->postJson(route('midterm-scores.store'), $scoreData);

        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Midterm score created successfully.');
        $response->assertJsonPath('data.score', 85.50);

        $this->assertDatabaseHas('midterm_scores', [
            'midterm_exam_id' => $exam->id,
            'student_id' => $student->id,
            'score' => 85.50,
        ]);
    }

    /**
     * Test validation rules on store.
     */
    public function test_store_midterm_score_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Test required fields
        $response = $this->actingAs($user)->post(route('midterm-scores.store'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'midterm_exam_id', 'student_id', 'score',
        ]);

        // Test invalid values
        $response = $this->actingAs($user)->post(route('midterm-scores.store'), [
            'midterm_exam_id' => 9999,
            'student_id' => 9999,
            'score' => 105,
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'midterm_exam_id', 'student_id', 'score',
        ]);

        $response2 = $this->actingAs($user)->post(route('midterm-scores.store'), [
            'midterm_exam_id' => 9999,
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
    public function test_authenticated_user_can_view_midterm_score_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = MidtermScores::factory()->create();

        $response = $this->actingAs($user)->getJson(route('midterm-scores.show', $score));

        $response->assertStatus(200);
        $response->assertJsonPath('id', $score->id);
        $response->assertJsonPath('score', $score->score);
    }

    /**
     * Test authenticated user can update midterm score.
     */
    public function test_authenticated_user_can_update_midterm_score(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = MidtermScores::factory()->create([
            'score' => 70.00,
        ]);
        $newExam = MidtermExams::factory()->create();
        $newStudent = Students::factory()->create();

        $updateData = [
            'midterm_exam_id' => $newExam->id,
            'student_id' => $newStudent->id,
            'score' => 90.50,
        ];

        $response = $this->actingAs($user)->putJson(route('midterm-scores.update', $score), $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Midterm score updated successfully.');
        $response->assertJsonPath('data.score', 90.50);

        $this->assertDatabaseHas('midterm_scores', [
            'id' => $score->id,
            'midterm_exam_id' => $newExam->id,
            'student_id' => $newStudent->id,
            'score' => 90.50,
        ]);
    }

    /**
     * Test authenticated user can destroy midterm score.
     */
    public function test_authenticated_user_can_delete_midterm_score_via_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = MidtermScores::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('midterm-scores.destroy', $score));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Midterm score deleted successfully.');

        $this->assertDatabaseMissing('midterm_scores', [
            'id' => $score->id,
        ]);
    }

    /**
     * Test authenticated user can delete via delete route.
     */
    public function test_authenticated_user_can_delete_midterm_score_via_delete_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = MidtermScores::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('midterm-scores.delete', $score));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Midterm score deleted successfully.');

        $this->assertDatabaseMissing('midterm_scores', [
            'id' => $score->id,
        ]);
    }
}
