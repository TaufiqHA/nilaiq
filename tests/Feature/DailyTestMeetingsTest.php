<?php

namespace Tests\Feature;

use App\Models\Classes;
use App\Models\DailyTestMeetings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyTestMeetingsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_daily_test_meetings(): void
    {
        $meeting = DailyTestMeetings::factory()->create();

        $this->get(route('daily-test-meetings.index'))->assertRedirect(route('login'));
        $this->get(route('daily-test-meetings.show', $meeting))->assertRedirect(route('login'));
        $this->post(route('daily-test-meetings.store'), [])->assertRedirect(route('login'));
        $this->put(route('daily-test-meetings.update', $meeting), [])->assertRedirect(route('login'));
        $this->delete(route('daily-test-meetings.destroy', $meeting))->assertRedirect(route('login'));
        $this->delete(route('daily-test-meetings.delete', $meeting))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access index.
     */
    public function test_authenticated_user_can_access_daily_test_meetings_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        DailyTestMeetings::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('daily-test-meetings.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Test authenticated user can store daily test meeting.
     */
    public function test_authenticated_user_can_store_daily_test_meeting(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $class = Classes::factory()->create();

        $meetingData = [
            'class_id' => $class->id,
            'title' => 'Ulangan Harian 1',
            'test_date' => '2026-07-18',
            'description' => 'Materi Bab 1 Aljabar.',
        ];

        $response = $this->actingAs($user)->postJson(route('daily-test-meetings.store'), $meetingData);

        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Daily test meeting created successfully.');
        $response->assertJsonPath('data.title', 'Ulangan Harian 1');

        $this->assertDatabaseHas('daily_test_meetings', [
            'class_id' => $class->id,
            'title' => 'Ulangan Harian 1',
            'description' => 'Materi Bab 1 Aljabar.',
        ]);

        $storedMeeting = DailyTestMeetings::first();
        $this->assertEquals('2026-07-18', $storedMeeting->test_date->format('Y-m-d'));
    }

    /**
     * Test validation rules on store.
     */
    public function test_store_daily_test_meeting_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Test required fields
        $response = $this->actingAs($user)->post(route('daily-test-meetings.store'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'class_id', 'title', 'test_date',
        ]);

        // Test invalid class_id
        $response = $this->actingAs($user)->post(route('daily-test-meetings.store'), [
            'class_id' => 9999,
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['class_id']);
    }

    /**
     * Test authenticated user can view details.
     */
    public function test_authenticated_user_can_view_daily_test_meeting_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $meeting = DailyTestMeetings::factory()->create();

        $response = $this->actingAs($user)->getJson(route('daily-test-meetings.show', $meeting));

        $response->assertStatus(200);
        $response->assertJsonPath('id', $meeting->id);
        $response->assertJsonPath('title', $meeting->title);
    }

    /**
     * Test authenticated user can update daily test meeting.
     */
    public function test_authenticated_user_can_update_daily_test_meeting(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $meeting = DailyTestMeetings::factory()->create([
            'title' => 'Ulangan Lama',
            'description' => 'Deskripsi lama.',
        ]);
        $newClass = Classes::factory()->create();

        $updateData = [
            'class_id' => $newClass->id,
            'title' => 'Ulangan Baru',
            'test_date' => '2026-07-19',
            'description' => 'Deskripsi baru.',
        ];

        $response = $this->actingAs($user)->putJson(route('daily-test-meetings.update', $meeting), $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Daily test meeting updated successfully.');
        $response->assertJsonPath('data.title', 'Ulangan Baru');
        $response->assertJsonPath('data.description', 'Deskripsi baru.');

        $this->assertDatabaseHas('daily_test_meetings', [
            'id' => $meeting->id,
            'class_id' => $newClass->id,
            'title' => 'Ulangan Baru',
            'description' => 'Deskripsi baru.',
        ]);

        $this->assertEquals('2026-07-19', $meeting->refresh()->test_date->format('Y-m-d'));
    }

    /**
     * Test authenticated user can destroy daily test meeting.
     */
    public function test_authenticated_user_can_delete_daily_test_meeting_via_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $meeting = DailyTestMeetings::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('daily-test-meetings.destroy', $meeting));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Daily test meeting deleted successfully.');

        $this->assertDatabaseMissing('daily_test_meetings', [
            'id' => $meeting->id,
        ]);
    }

    /**
     * Test authenticated user can delete via delete route.
     */
    public function test_authenticated_user_can_delete_daily_test_meeting_via_delete_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $meeting = DailyTestMeetings::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('daily-test-meetings.delete', $meeting));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Daily test meeting deleted successfully.');

        $this->assertDatabaseMissing('daily_test_meetings', [
            'id' => $meeting->id,
        ]);
    }
}
