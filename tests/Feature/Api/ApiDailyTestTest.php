<?php

namespace Tests\Feature\Api;

use App\Models\Classes;
use App\Models\DailyTestMeetings;
use App\Models\DailyTestScores;
use App\Models\Students;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiDailyTestTest extends TestCase
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
     * Guests cannot access the daily test endpoints.
     */
    public function test_guest_cannot_access_daily_test_endpoints(): void
    {
        $this->getJson('/api/daily-test-meetings')->assertStatus(401);
        $this->postJson('/api/daily-test-meetings', [])->assertStatus(401);
        $this->getJson('/api/daily-test-scores')->assertStatus(401);
        $this->postJson('/api/daily-test-scores', [])->assertStatus(401);
    }

    /**
     * Daily test meetings index includes class students and scores.
     */
    public function test_daily_test_meetings_index_returns_relations(): void
    {
        $this->actingAsMapel();
        DailyTestMeetings::factory()->create();

        $response = $this->getJson('/api/daily-test-meetings');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            ['id', 'title', 'test_date', 'class' => ['id', 'name', 'students'], 'scores'],
        ]);
    }

    /**
     * Storing a daily test meeting persists it.
     */
    public function test_can_store_daily_test_meeting(): void
    {
        $this->actingAsMapel();
        $class = Classes::factory()->create();

        $response = $this->postJson('/api/daily-test-meetings', [
            'class_id' => $class->id,
            'title' => 'Ulangan Harian Bab 1',
            'test_date' => '2026-07-18',
            'description' => 'Materi bab 1.',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('daily_test_meetings', [
            'class_id' => $class->id,
            'title' => 'Ulangan Harian Bab 1',
        ]);
    }

    /**
     * Storing with the same client_uuid is idempotent (safe to retry).
     */
    public function test_storing_daily_test_meeting_is_idempotent_with_client_uuid(): void
    {
        $this->actingAsMapel();
        $class = Classes::factory()->create();
        $payload = [
            'client_uuid' => '22222222-2222-4222-8222-222222222222',
            'class_id' => $class->id,
            'title' => 'Ulangan Offline',
            'test_date' => '2026-07-18',
            'description' => 'Dibuat offline.',
        ];

        $this->postJson('/api/daily-test-meetings', $payload)->assertStatus(201);
        $this->postJson('/api/daily-test-meetings', $payload)->assertStatus(200);

        $this->assertDatabaseCount('daily_test_meetings', 1);
    }

    /**
     * A daily test meeting can be updated and deleted.
     */
    public function test_can_update_and_delete_daily_test_meeting(): void
    {
        $this->actingAsMapel();
        $meeting = DailyTestMeetings::factory()->create();
        $class = Classes::factory()->create();

        $this->putJson('/api/daily-test-meetings/'.$meeting->id, [
            'class_id' => $class->id,
            'title' => 'Ulangan Harian Baru',
            'test_date' => '2026-07-19',
            'description' => 'Deskripsi baru.',
        ])->assertStatus(200)->assertJsonPath('data.title', 'Ulangan Harian Baru');

        $this->deleteJson('/api/daily-test-meetings/'.$meeting->id)->assertStatus(200);
        $this->assertDatabaseMissing('daily_test_meetings', ['id' => $meeting->id]);
    }

    /**
     * Storing a daily test score creates a record.
     */
    public function test_can_store_daily_test_score(): void
    {
        $this->actingAsMapel();
        $meeting = DailyTestMeetings::factory()->create();
        $student = Students::factory()->create();

        $response = $this->postJson('/api/daily-test-scores', [
            'daily_test_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'score' => 88.5,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('daily_test_scores', [
            'daily_test_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'score' => 88.5,
        ]);
    }

    /**
     * Storing a score for the same meeting+student upserts instead of duplicating.
     */
    public function test_storing_daily_test_score_upserts_for_same_student_and_meeting(): void
    {
        $this->actingAsMapel();
        $meeting = DailyTestMeetings::factory()->create();
        $student = Students::factory()->create();

        $this->postJson('/api/daily-test-scores', [
            'daily_test_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'score' => 70,
        ])->assertStatus(201);

        $this->postJson('/api/daily-test-scores', [
            'daily_test_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'score' => 95,
        ])->assertStatus(200);

        $this->assertDatabaseCount('daily_test_scores', 1);
        $this->assertDatabaseHas('daily_test_scores', [
            'daily_test_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'score' => 95,
        ]);
    }

    /**
     * A daily test score can be updated and deleted.
     */
    public function test_can_update_and_delete_daily_test_score(): void
    {
        $this->actingAsMapel();
        $meeting = DailyTestMeetings::factory()->create();
        $student = Students::factory()->create();
        $score = DailyTestScores::factory()->create([
            'daily_test_meeting_id' => $meeting->id,
            'student_id' => $student->id,
        ]);

        $this->putJson('/api/daily-test-scores/'.$score->id, [
            'daily_test_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'score' => 60,
        ])->assertStatus(200)->assertJsonPath('data.score', 60);

        $this->deleteJson('/api/daily-test-scores/'.$score->id)->assertStatus(200);
        $this->assertDatabaseMissing('daily_test_scores', ['id' => $score->id]);
    }
}
