<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AssignmentMeetings;
use App\Models\AssignmentScores;
use App\Models\Classes;
use App\Models\DailyTestMeetings;
use App\Models\DailyTestScores;
use App\Models\FinalExams;
use App\Models\FinalScores;
use App\Models\MidtermExams;
use App\Models\MidtermScores;
use App\Models\Students;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekapControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest is redirected to login.
     */
    public function test_guest_cannot_access_rekap(): void
    {
        $class = Classes::factory()->create();

        $this->get(route('rekap.index'))->assertRedirect(route('login'));
        $this->get(route('rekap.data', $class))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access rekap index page.
     */
    public function test_authenticated_user_can_access_rekap_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Classes::factory()->count(2)->create();

        $response = $this->actingAs($user)->get(route('rekap.index'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.rekap');
        $response->assertViewHas('classes');
    }

    /**
     * Test score recap aggregation, averages, total, and rankings.
     */
    public function test_authenticated_user_can_get_class_rekap_data(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var AcademicYear $academicYear */
        $academicYear = AcademicYear::factory()->create();

        /** @var Classes $class */
        $class = Classes::factory()->create([
            'academic_year_id' => $academicYear->id,
        ]);

        // Create 2 students
        /** @var Students $studentA */
        $studentA = Students::factory()->create([
            'class_id' => $class->id,
            'name' => 'Student A',
            'status' => 'ACTIVE',
        ]);

        /** @var Students $studentB */
        $studentB = Students::factory()->create([
            'class_id' => $class->id,
            'name' => 'Student B',
            'status' => 'ACTIVE',
        ]);

        // 1. Daily Tests
        $dailyMeeting = DailyTestMeetings::factory()->create(['class_id' => $class->id]);
        DailyTestScores::factory()->create([
            'daily_test_meeting_id' => $dailyMeeting->id,
            'student_id' => $studentA->id,
            'score' => 90.00,
        ]);
        DailyTestScores::factory()->create([
            'daily_test_meeting_id' => $dailyMeeting->id,
            'student_id' => $studentB->id,
            'score' => 80.00,
        ]);

        // 2. Assignments
        $assignmentMeeting = AssignmentMeetings::factory()->create(['class_id' => $class->id]);
        AssignmentScores::factory()->create([
            'assignment_meeting_id' => $assignmentMeeting->id,
            'student_id' => $studentA->id,
            'score' => 95.00,
        ]);
        AssignmentScores::factory()->create([
            'assignment_meeting_id' => $assignmentMeeting->id,
            'student_id' => $studentB->id,
            'score' => 85.00,
        ]);

        // 3. Midterm
        $midterm = MidtermExams::factory()->create(['class_id' => $class->id]);
        MidtermScores::factory()->create([
            'midterm_exam_id' => $midterm->id,
            'student_id' => $studentA->id,
            'score' => 100.00,
        ]);
        MidtermScores::factory()->create([
            'midterm_exam_id' => $midterm->id,
            'student_id' => $studentB->id,
            'score' => 90.00,
        ]);

        // 4. Final
        $final = FinalExams::factory()->create(['class_id' => $class->id]);
        FinalScores::factory()->create([
            'final_exam_id' => $final->id,
            'student_id' => $studentA->id,
            'score' => 90.00,
        ]);
        FinalScores::factory()->create([
            'final_exam_id' => $final->id,
            'student_id' => $studentB->id,
            'score' => 80.00,
        ]);

        // Fetch recap JSON data
        $response = $this->actingAs($user)->getJson(route('rekap.data', $class));

        $response->assertStatus(200);

        // Verify Class and Meetings structure in response
        $response->assertJsonPath('class.id', $class->id);
        $response->assertJsonCount(1, 'meetings.daily_tests');
        $response->assertJsonCount(1, 'meetings.assignments');

        // Verify aggregated scores and rank for Student A (expected avg: 93.75, rank: 1)
        $response->assertJsonFragment([
            'student' => [
                'id' => $studentA->id,
                'class_id' => $class->id,
                'nis' => $studentA->nis,
                'nisn' => $studentA->nisn,
                'name' => 'Student A',
                'gender' => $studentA->gender,
                'birth_place' => $studentA->birth_place,
                'birth_date' => $studentA->birth_date->toISOString(),
                'address' => $studentA->address,
                'parent_name' => $studentA->parent_name,
                'parent_phone' => $studentA->parent_phone,
                'status' => 'ACTIVE',
                'created_at' => $studentA->created_at->toISOString(),
                'updated_at' => $studentA->updated_at->toISOString(),
            ],
            'scores' => [
                'daily_tests' => [
                    (string) $dailyMeeting->id => 90,
                ],
                'assignments' => [
                    (string) $assignmentMeeting->id => 95,
                ],
                'midterms' => [
                    (string) $midterm->id => 100,
                ],
                'finals' => [
                    (string) $final->id => 90,
                ],
            ],
            'jumlah' => 375,
            'rata_rata' => 93.75,
            'peringkat' => 1,
        ]);

        // Verify aggregated scores and rank for Student B (expected avg: 83.75, rank: 2)
        $response->assertJsonFragment([
            'student' => [
                'id' => $studentB->id,
                'class_id' => $class->id,
                'nis' => $studentB->nis,
                'nisn' => $studentB->nisn,
                'name' => 'Student B',
                'gender' => $studentB->gender,
                'birth_place' => $studentB->birth_place,
                'birth_date' => $studentB->birth_date->toISOString(),
                'address' => $studentB->address,
                'parent_name' => $studentB->parent_name,
                'parent_phone' => $studentB->parent_phone,
                'status' => 'ACTIVE',
                'created_at' => $studentB->created_at->toISOString(),
                'updated_at' => $studentB->updated_at->toISOString(),
            ],
            'scores' => [
                'daily_tests' => [
                    (string) $dailyMeeting->id => 80,
                ],
                'assignments' => [
                    (string) $assignmentMeeting->id => 85,
                ],
                'midterms' => [
                    (string) $midterm->id => 90,
                ],
                'finals' => [
                    (string) $final->id => 80,
                ],
            ],
            'jumlah' => 335,
            'rata_rata' => 83.75,
            'peringkat' => 2,
        ]);
    }
}
