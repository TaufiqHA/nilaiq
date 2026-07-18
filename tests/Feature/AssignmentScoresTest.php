<?php

namespace Tests\Feature;

use App\Models\AssignmentMeetings;
use App\Models\AssignmentScores;
use App\Models\Students;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentScoresTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_assignment_scores(): void
    {
        $score = AssignmentScores::factory()->create();

        $this->get(route('assignment-scores.index'))->assertRedirect(route('login'));
        $this->get(route('assignment-scores.show', $score))->assertRedirect(route('login'));
        $this->post(route('assignment-scores.store'), [])->assertRedirect(route('login'));
        $this->put(route('assignment-scores.update', $score), [])->assertRedirect(route('login'));
        $this->delete(route('assignment-scores.destroy', $score))->assertRedirect(route('login'));
        $this->delete(route('assignment-scores.delete', $score))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access index.
     */
    public function test_authenticated_user_can_access_assignment_scores_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        AssignmentScores::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('assignment-scores.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Test authenticated user can store assignment score.
     */
    public function test_authenticated_user_can_store_assignment_score(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $meeting = AssignmentMeetings::factory()->create();
        $student = Students::factory()->create();

        $scoreData = [
            'assignment_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'score' => 85.50,
        ];

        $response = $this->actingAs($user)->postJson(route('assignment-scores.store'), $scoreData);

        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Assignment score created successfully.');
        $response->assertJsonPath('data.score', 85.50);

        $this->assertDatabaseHas('assignment_scores', [
            'assignment_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'score' => 85.50,
        ]);
    }

    /**
     * Test validation rules on store.
     */
    public function test_store_assignment_score_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Test required fields
        $response = $this->actingAs($user)->post(route('assignment-scores.store'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'assignment_meeting_id', 'student_id', 'score',
        ]);

        // Test invalid values
        $response = $this->actingAs($user)->post(route('assignment-scores.store'), [
            'assignment_meeting_id' => 9999,
            'student_id' => 9999,
            'score' => 105,
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'assignment_meeting_id', 'student_id', 'score',
        ]);

        $response2 = $this->actingAs($user)->post(route('assignment-scores.store'), [
            'assignment_meeting_id' => 9999,
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
    public function test_authenticated_user_can_view_assignment_score_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = AssignmentScores::factory()->create();

        $response = $this->actingAs($user)->getJson(route('assignment-scores.show', $score));

        $response->assertStatus(200);
        $response->assertJsonPath('id', $score->id);
        $response->assertJsonPath('score', $score->score);
    }

    /**
     * Test authenticated user can update assignment score.
     */
    public function test_authenticated_user_can_update_assignment_score(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = AssignmentScores::factory()->create([
            'score' => 70.00,
        ]);
        $newMeeting = AssignmentMeetings::factory()->create();
        $newStudent = Students::factory()->create();

        $updateData = [
            'assignment_meeting_id' => $newMeeting->id,
            'student_id' => $newStudent->id,
            'score' => 90.50,
        ];

        $response = $this->actingAs($user)->putJson(route('assignment-scores.update', $score), $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Assignment score updated successfully.');
        $response->assertJsonPath('data.score', 90.50);

        $this->assertDatabaseHas('assignment_scores', [
            'id' => $score->id,
            'assignment_meeting_id' => $newMeeting->id,
            'student_id' => $newStudent->id,
            'score' => 90.50,
        ]);
    }

    /**
     * Test authenticated user can destroy assignment score.
     */
    public function test_authenticated_user_can_delete_assignment_score_via_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = AssignmentScores::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('assignment-scores.destroy', $score));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Assignment score deleted successfully.');

        $this->assertDatabaseMissing('assignment_scores', [
            'id' => $score->id,
        ]);
    }

    /**
     * Test authenticated user can delete via delete route.
     */
    public function test_authenticated_user_can_delete_assignment_score_via_delete_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = AssignmentScores::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('assignment-scores.delete', $score));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Assignment score deleted successfully.');

        $this->assertDatabaseMissing('assignment_scores', [
            'id' => $score->id,
        ]);
    }
}
