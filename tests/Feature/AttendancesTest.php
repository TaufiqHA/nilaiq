<?php

namespace Tests\Feature;

use App\Models\AttendanceMeetings;
use App\Models\Attendances;
use App\Models\Students;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendancesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_attendances(): void
    {
        $attendance = Attendances::factory()->create();

        $this->get(route('attendances.index'))->assertRedirect(route('login'));
        $this->get(route('attendances.show', $attendance))->assertRedirect(route('login'));
        $this->post(route('attendances.store'), [])->assertRedirect(route('login'));
        $this->put(route('attendances.update', $attendance), [])->assertRedirect(route('login'));
        $this->delete(route('attendances.destroy', $attendance))->assertRedirect(route('login'));
        $this->delete(route('attendances.delete', $attendance))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access index.
     */
    public function test_authenticated_user_can_access_attendances_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Attendances::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('attendances.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Test authenticated user can store attendance.
     */
    public function test_authenticated_user_can_store_attendance(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $meeting = AttendanceMeetings::factory()->create();
        $student = Students::factory()->create();

        $attendanceData = [
            'attendance_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'status' => 'HADIR',
            'note' => 'Hadir tepat waktu.',
        ];

        $response = $this->actingAs($user)->postJson(route('attendances.store'), $attendanceData);

        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Attendance created successfully.');
        $response->assertJsonPath('data.status', 'HADIR');
        $response->assertJsonPath('data.note', 'Hadir tepat waktu.');

        $this->assertDatabaseHas('attendances', [
            'attendance_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'status' => 'HADIR',
            'note' => 'Hadir tepat waktu.',
        ]);
    }

    /**
     * Test validation rules on store.
     */
    public function test_store_attendance_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Test required fields
        $response = $this->actingAs($user)->post(route('attendances.store'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'attendance_meeting_id', 'student_id', 'status',
        ]);

        // Test invalid values
        $response = $this->actingAs($user)->post(route('attendances.store'), [
            'attendance_meeting_id' => 9999,
            'student_id' => 9999,
            'status' => 'BOLOS',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'attendance_meeting_id', 'student_id', 'status',
        ]);
    }

    /**
     * Test authenticated user can view details.
     */
    public function test_authenticated_user_can_view_attendance_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $attendance = Attendances::factory()->create();

        $response = $this->actingAs($user)->getJson(route('attendances.show', $attendance));

        $response->assertStatus(200);
        $response->assertJsonPath('id', $attendance->id);
        $response->assertJsonPath('status', $attendance->status);
    }

    /**
     * Test authenticated user can update attendance.
     */
    public function test_authenticated_user_can_update_attendance(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $attendance = Attendances::factory()->create([
            'status' => 'IZIN',
            'note' => 'Izin sakit gigi.',
        ]);
        $newMeeting = AttendanceMeetings::factory()->create();
        $newStudent = Students::factory()->create();

        $updateData = [
            'attendance_meeting_id' => $newMeeting->id,
            'student_id' => $newStudent->id,
            'status' => 'SAKIT',
            'note' => 'Ada surat dokter.',
        ];

        $response = $this->actingAs($user)->putJson(route('attendances.update', $attendance), $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Attendance updated successfully.');
        $response->assertJsonPath('data.status', 'SAKIT');
        $response->assertJsonPath('data.note', 'Ada surat dokter.');

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'attendance_meeting_id' => $newMeeting->id,
            'student_id' => $newStudent->id,
            'status' => 'SAKIT',
            'note' => 'Ada surat dokter.',
        ]);
    }

    /**
     * Test authenticated user can destroy attendance.
     */
    public function test_authenticated_user_can_delete_attendance_via_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $attendance = Attendances::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('attendances.destroy', $attendance));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Attendance deleted successfully.');

        $this->assertDatabaseMissing('attendances', [
            'id' => $attendance->id,
        ]);
    }

    /**
     * Test authenticated user can delete via delete route.
     */
    public function test_authenticated_user_can_delete_attendance_via_delete_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $attendance = Attendances::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('attendances.delete', $attendance));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Attendance deleted successfully.');

        $this->assertDatabaseMissing('attendances', [
            'id' => $attendance->id,
        ]);
    }
}
