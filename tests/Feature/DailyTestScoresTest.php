<?php

namespace Tests\Feature;

use App\Models\DailyTestMeetings;
use App\Models\DailyTestScores;
use App\Models\Students;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyTestScoresTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_daily_test_scores(): void
    {
        $score = DailyTestScores::factory()->create();

        $this->get(route('daily-test-scores.index'))->assertRedirect(route('login'));
        $this->get(route('daily-test-scores.show', $score))->assertRedirect(route('login'));
        $this->post(route('daily-test-scores.store'), [])->assertRedirect(route('login'));
        $this->put(route('daily-test-scores.update', $score), [])->assertRedirect(route('login'));
        $this->delete(route('daily-test-scores.destroy', $score))->assertRedirect(route('login'));
        $this->delete(route('daily-test-scores.delete', $score))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access index.
     */
    public function test_authenticated_user_can_access_daily_test_scores_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        DailyTestScores::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('daily-test-scores.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Test authenticated user can store daily test score.
     */
    public function test_authenticated_user_can_store_daily_test_score(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $meeting = DailyTestMeetings::factory()->create();
        $student = Students::factory()->create();

        $scoreData = [
            'daily_test_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'score' => 85.50,
        ];

        $response = $this->actingAs($user)->postJson(route('daily-test-scores.store'), $scoreData);

        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Daily test score created successfully.');
        $response->assertJsonPath('data.score', 85.50);

        $this->assertDatabaseHas('daily_test_scores', [
            'daily_test_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'score' => 85.50,
        ]);
    }

    /**
     * Test validation rules on store.
     */
    public function test_store_daily_test_score_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Test required fields
        $response = $this->actingAs($user)->post(route('daily-test-scores.store'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'daily_test_meeting_id', 'student_id', 'score',
        ]);

        // Test invalid values
        $response = $this->actingAs($user)->post(route('daily-test-scores.store'), [
            'daily_test_meeting_id' => 9999,
            'student_id' => 9999,
            'score' => 105,
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'daily_test_meeting_id', 'student_id', 'score',
        ]);

        $response2 = $this->actingAs($user)->post(route('daily-test-scores.store'), [
            'daily_test_meeting_id' => 9999,
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
    public function test_authenticated_user_can_view_daily_test_score_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = DailyTestScores::factory()->create();

        $response = $this->actingAs($user)->getJson(route('daily-test-scores.show', $score));

        $response->assertStatus(200);
        $response->assertJsonPath('id', $score->id);
        $response->assertJsonPath('score', $score->score);
    }

    /**
     * Test authenticated user can update daily test score.
     */
    public function test_authenticated_user_can_update_daily_test_score(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = DailyTestScores::factory()->create([
            'score' => 70.00,
        ]);
        $newMeeting = DailyTestMeetings::factory()->create();
        $newStudent = Students::factory()->create();

        $updateData = [
            'daily_test_meeting_id' => $newMeeting->id,
            'student_id' => $newStudent->id,
            'score' => 90.50,
        ];

        $response = $this->actingAs($user)->putJson(route('daily-test-scores.update', $score), $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Daily test score updated successfully.');
        $response->assertJsonPath('data.score', 90.50);

        $this->assertDatabaseHas('daily_test_scores', [
            'id' => $score->id,
            'daily_test_meeting_id' => $newMeeting->id,
            'student_id' => $newStudent->id,
            'score' => 90.50,
        ]);
    }

    /**
     * Test authenticated user can destroy daily test score.
     */
    public function test_authenticated_user_can_delete_daily_test_score_via_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = DailyTestScores::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('daily-test-scores.destroy', $score));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Daily test score deleted successfully.');

        $this->assertDatabaseMissing('daily_test_scores', [
            'id' => $score->id,
        ]);
    }

    /**
     * Test authenticated user can delete via delete route.
     */
    public function test_authenticated_user_can_delete_daily_test_score_via_delete_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $score = DailyTestScores::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('daily-test-scores.delete', $score));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Daily test score deleted successfully.');

        $this->assertDatabaseMissing('daily_test_scores', [
            'id' => $score->id,
        ]);
    }
}
