<?php

namespace Tests\Feature;

use App\Models\AttendanceMeetings;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceMeetingsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_attendance_meetings(): void
    {
        $meeting = AttendanceMeetings::factory()->create();

        $this->get(route('attendance-meetings.index'))->assertRedirect(route('login'));
        $this->get(route('attendance-meetings.show', $meeting))->assertRedirect(route('login'));
        $this->post(route('attendance-meetings.store'), [])->assertRedirect(route('login'));
        $this->put(route('attendance-meetings.update', $meeting), [])->assertRedirect(route('login'));
        $this->delete(route('attendance-meetings.destroy', $meeting))->assertRedirect(route('login'));
        $this->delete(route('attendance-meetings.delete', $meeting))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access index.
     */
    public function test_authenticated_user_can_access_attendance_meetings_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        AttendanceMeetings::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('attendance-meetings.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Test authenticated user can store attendance meeting.
     */
    public function test_authenticated_user_can_store_attendance_meeting(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        $class = Classes::factory()->create();

        $meetingData = [
            'class_id' => $class->id,
            'title' => 'Pertemuan Pertama',
            'meeting_date' => '2026-07-18',
            'description' => 'Membahas pengenalan materi kelas.',
            'tipe' => 'harian',
        ];

        $response = $this->actingAs($user)->postJson(route('attendance-meetings.store'), $meetingData);

        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Attendance meeting created successfully.');
        $response->assertJsonPath('data.title', 'Pertemuan Pertama');
        $response->assertJsonPath('data.tipe', 'harian');

        $this->assertDatabaseHas('attendance_meetings', [
            'class_id' => $class->id,
            'title' => 'Pertemuan Pertama',
            'description' => 'Membahas pengenalan materi kelas.',
            'tipe' => 'harian',
        ]);

        $storedMeeting = AttendanceMeetings::first();
        $this->assertEquals('2026-07-18', $storedMeeting->meeting_date->format('Y-m-d'));
    }

    /**
     * Test validation rules on store.
     */
    public function test_store_attendance_meeting_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);

        // Test required fields
        $response = $this->actingAs($user)->post(route('attendance-meetings.store'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'class_id', 'title', 'meeting_date', 'tipe',
        ]);

        // Test invalid class_id
        $response = $this->actingAs($user)->post(route('attendance-meetings.store'), [
            'class_id' => 9999,
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['class_id']);
    }

    /**
     * Test authenticated user can view details.
     */
    public function test_authenticated_user_can_view_attendance_meeting_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        $meeting = AttendanceMeetings::factory()->create();

        $response = $this->actingAs($user)->getJson(route('attendance-meetings.show', $meeting));

        $response->assertStatus(200);
        $response->assertJsonPath('id', $meeting->id);
        $response->assertJsonPath('title', $meeting->title);
    }

    /**
     * Test authenticated user can update attendance meeting.
     */
    public function test_authenticated_user_can_update_attendance_meeting(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        $meeting = AttendanceMeetings::factory()->create([
            'title' => 'Pertemuan Lama',
            'description' => 'Deskripsi lama.',
            'tipe' => 'harian',
        ]);
        $newClass = Classes::factory()->create();

        $updateData = [
            'class_id' => $newClass->id,
            'title' => 'Pertemuan Baru',
            'meeting_date' => '2026-07-19',
            'description' => 'Deskripsi baru.',
            'tipe' => 'tugas',
        ];

        $response = $this->actingAs($user)->putJson(route('attendance-meetings.update', $meeting), $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Attendance meeting updated successfully.');
        $response->assertJsonPath('data.title', 'Pertemuan Baru');
        $response->assertJsonPath('data.description', 'Deskripsi baru.');
        $response->assertJsonPath('data.tipe', 'tugas');

        $this->assertDatabaseHas('attendance_meetings', [
            'id' => $meeting->id,
            'class_id' => $newClass->id,
            'title' => 'Pertemuan Baru',
            'description' => 'Deskripsi baru.',
            'tipe' => 'tugas',
        ]);

        $this->assertEquals('2026-07-19', $meeting->refresh()->meeting_date->format('Y-m-d'));
    }

    /**
     * Test authenticated user can destroy attendance meeting.
     */
    public function test_authenticated_user_can_delete_attendance_meeting_via_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        $meeting = AttendanceMeetings::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('attendance-meetings.destroy', $meeting));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Attendance meeting deleted successfully.');

        $this->assertDatabaseMissing('attendance_meetings', [
            'id' => $meeting->id,
        ]);
    }

    /**
     * Test authenticated user can delete via delete route.
     */
    public function test_authenticated_user_can_delete_attendance_meeting_via_delete_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        $meeting = AttendanceMeetings::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('attendance-meetings.delete', $meeting));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Attendance meeting deleted successfully.');

        $this->assertDatabaseMissing('attendance_meetings', [
            'id' => $meeting->id,
        ]);
    }
}
