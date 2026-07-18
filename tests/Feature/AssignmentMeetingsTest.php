<?php

namespace Tests\Feature;

use App\Models\AssignmentMeetings;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentMeetingsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_assignment_meetings(): void
    {
        $meeting = AssignmentMeetings::factory()->create();

        $this->get(route('assignment-meetings.index'))->assertRedirect(route('login'));
        $this->get(route('assignment-meetings.show', $meeting))->assertRedirect(route('login'));
        $this->post(route('assignment-meetings.store'), [])->assertRedirect(route('login'));
        $this->put(route('assignment-meetings.update', $meeting), [])->assertRedirect(route('login'));
        $this->delete(route('assignment-meetings.destroy', $meeting))->assertRedirect(route('login'));
        $this->delete(route('assignment-meetings.delete', $meeting))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access index.
     */
    public function test_authenticated_user_can_access_assignment_meetings_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        AssignmentMeetings::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('assignment-meetings.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Test authenticated user can store assignment meeting.
     */
    public function test_authenticated_user_can_store_assignment_meeting(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $class = Classes::factory()->create();

        $meetingData = [
            'class_id' => $class->id,
            'title' => 'Tugas Matematika 1',
            'assignment_date' => '2026-07-18',
            'description' => 'Materi Bab 1 Aljabar.',
        ];

        $response = $this->actingAs($user)->postJson(route('assignment-meetings.store'), $meetingData);

        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Assignment meeting created successfully.');
        $response->assertJsonPath('data.title', 'Tugas Matematika 1');

        $this->assertDatabaseHas('assignment_meetings', [
            'class_id' => $class->id,
            'title' => 'Tugas Matematika 1',
            'description' => 'Materi Bab 1 Aljabar.',
        ]);

        $storedMeeting = AssignmentMeetings::first();
        $this->assertEquals('2026-07-18', $storedMeeting->assignment_date->format('Y-m-d'));
    }

    /**
     * Test validation rules on store.
     */
    public function test_store_assignment_meeting_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Test required fields
        $response = $this->actingAs($user)->post(route('assignment-meetings.store'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'class_id', 'title', 'assignment_date',
        ]);

        // Test invalid class_id
        $response = $this->actingAs($user)->post(route('assignment-meetings.store'), [
            'class_id' => 9999,
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['class_id']);
    }

    /**
     * Test authenticated user can view details.
     */
    public function test_authenticated_user_can_view_assignment_meeting_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $meeting = AssignmentMeetings::factory()->create();

        $response = $this->actingAs($user)->getJson(route('assignment-meetings.show', $meeting));

        $response->assertStatus(200);
        $response->assertJsonPath('id', $meeting->id);
        $response->assertJsonPath('title', $meeting->title);
    }

    /**
     * Test authenticated user can update assignment meeting.
     */
    public function test_authenticated_user_can_update_assignment_meeting(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $meeting = AssignmentMeetings::factory()->create([
            'title' => 'Tugas Lama',
            'description' => 'Deskripsi lama.',
        ]);
        $newClass = Classes::factory()->create();

        $updateData = [
            'class_id' => $newClass->id,
            'title' => 'Tugas Baru',
            'assignment_date' => '2026-07-19',
            'description' => 'Deskripsi baru.',
        ];

        $response = $this->actingAs($user)->putJson(route('assignment-meetings.update', $meeting), $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Assignment meeting updated successfully.');
        $response->assertJsonPath('data.title', 'Tugas Baru');
        $response->assertJsonPath('data.description', 'Deskripsi baru.');

        $this->assertDatabaseHas('assignment_meetings', [
            'id' => $meeting->id,
            'class_id' => $newClass->id,
            'title' => 'Tugas Baru',
            'description' => 'Deskripsi baru.',
        ]);

        $this->assertEquals('2026-07-19', $meeting->refresh()->assignment_date->format('Y-m-d'));
    }

    /**
     * Test authenticated user can destroy assignment meeting.
     */
    public function test_authenticated_user_can_delete_assignment_meeting_via_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $meeting = AssignmentMeetings::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('assignment-meetings.destroy', $meeting));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Assignment meeting deleted successfully.');

        $this->assertDatabaseMissing('assignment_meetings', [
            'id' => $meeting->id,
        ]);
    }

    /**
     * Test authenticated user can delete via delete route.
     */
    public function test_authenticated_user_can_delete_assignment_meeting_via_delete_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $meeting = AssignmentMeetings::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('assignment-meetings.delete', $meeting));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Assignment meeting deleted successfully.');

        $this->assertDatabaseMissing('assignment_meetings', [
            'id' => $meeting->id,
        ]);
    }
}
