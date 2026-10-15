<?php

namespace Tests\Feature\Api;

use App\Models\AttendanceMeetings;
use App\Models\Attendances;
use App\Models\Classes;
use App\Models\Students;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAbsensiTest extends TestCase
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
     * Dashboard returns the expected payload for the mobile app.
     */
    public function test_dashboard_returns_expected_payload(): void
    {
        $this->actingAsMapel();

        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'totalStudents',
            'totalClasses',
            'attendanceRate',
            'dailyTestCount',
            'assignmentCount',
            'midtermCount',
            'finalCount',
            'latestStudents',
            'recentActivities',
        ]);
    }

    /**
     * Classes endpoint returns a list of classes.
     */
    public function test_classes_endpoint_returns_list(): void
    {
        $this->actingAsMapel();
        Classes::factory()->count(2)->create();

        $this->getJson('/api/classes')->assertStatus(200);
    }

    /**
     * Attendance meetings index includes class students and attendances.
     */
    public function test_attendance_meetings_index_returns_relations(): void
    {
        $this->actingAsMapel();
        AttendanceMeetings::factory()->create();

        $response = $this->getJson('/api/attendance-meetings');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            ['id', 'title', 'meeting_date', 'tipe', 'class' => ['id', 'name', 'students'], 'attendances'],
        ]);
    }

    /**
     * Storing a meeting persists it.
     */
    public function test_can_store_attendance_meeting(): void
    {
        $this->actingAsMapel();
        $class = Classes::factory()->create();

        $response = $this->postJson('/api/attendance-meetings', [
            'class_id' => $class->id,
            'title' => 'Pertemuan Pertama',
            'meeting_date' => '2026-07-18',
            'description' => 'Membahas pengenalan materi.',
            'tipe' => 'harian',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('attendance_meetings', [
            'class_id' => $class->id,
            'title' => 'Pertemuan Pertama',
        ]);
    }

    /**
     * Storing with the same client_uuid is idempotent (safe to retry).
     */
    public function test_storing_meeting_is_idempotent_with_client_uuid(): void
    {
        $this->actingAsMapel();
        $class = Classes::factory()->create();
        $payload = [
            'client_uuid' => '11111111-1111-4111-8111-111111111111',
            'class_id' => $class->id,
            'title' => 'Pertemuan Offline',
            'meeting_date' => '2026-07-18',
            'description' => 'Dibuat offline.',
            'tipe' => 'harian',
        ];

        $this->postJson('/api/attendance-meetings', $payload)->assertStatus(201);
        $this->postJson('/api/attendance-meetings', $payload)->assertStatus(200);

        $this->assertDatabaseCount('attendance_meetings', 1);
    }

    /**
     * A meeting can be updated and deleted.
     */
    public function test_can_update_and_delete_attendance_meeting(): void
    {
        $this->actingAsMapel();
        $meeting = AttendanceMeetings::factory()->create();
        $class = Classes::factory()->create();

        $this->putJson('/api/attendance-meetings/'.$meeting->id, [
            'class_id' => $class->id,
            'title' => 'Pertemuan Baru',
            'meeting_date' => '2026-07-19',
            'description' => 'Deskripsi baru.',
            'tipe' => 'tugas',
        ])->assertStatus(200)->assertJsonPath('data.title', 'Pertemuan Baru');

        $this->deleteJson('/api/attendance-meetings/'.$meeting->id)->assertStatus(200);
        $this->assertDatabaseMissing('attendance_meetings', ['id' => $meeting->id]);
    }

    /**
     * Storing attendance creates a record.
     */
    public function test_can_store_attendance(): void
    {
        $this->actingAsMapel();
        $meeting = AttendanceMeetings::factory()->create();
        $student = Students::factory()->create();

        $response = $this->postJson('/api/attendances', [
            'attendance_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'status' => 'HADIR',
            'note' => 'Tepat waktu',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('attendances', [
            'attendance_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'status' => 'HADIR',
        ]);
    }

    /**
     * Storing attendance for the same meeting+student upserts instead of duplicating.
     */
    public function test_storing_attendance_upserts_for_same_student_and_meeting(): void
    {
        $this->actingAsMapel();
        $meeting = AttendanceMeetings::factory()->create();
        $student = Students::factory()->create();

        $this->postJson('/api/attendances', [
            'attendance_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'status' => 'HADIR',
        ])->assertStatus(201);

        $this->postJson('/api/attendances', [
            'attendance_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'status' => 'SAKIT',
            'note' => 'Demam',
        ])->assertStatus(200);

        $this->assertDatabaseCount('attendances', 1);
        $this->assertDatabaseHas('attendances', [
            'attendance_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'status' => 'SAKIT',
            'note' => 'Demam',
        ]);
    }

    /**
     * Rekap absensi returns JSON when requested by the mobile app.
     */
    public function test_rekap_absensi_returns_json(): void
    {
        $this->actingAsMapel();
        $class = Classes::factory()->create();
        $meeting = AttendanceMeetings::factory()->create(['class_id' => $class->id]);
        $student = Students::factory()->create(['class_id' => $class->id]);
        Attendances::factory()->create([
            'attendance_meeting_id' => $meeting->id,
            'student_id' => $student->id,
        ]);

        $response = $this->getJson('/api/rekap-absensi?class_id='.$class->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'class' => ['id', 'name'],
            'students',
            'meetings',
            'attendances',
        ]);
    }
}
