<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\AcademicYear;
use App\Models\AttendanceMeetings;
use App\Models\Attendances;
use App\Models\Classes;
use App\Models\ClassWaliKelas;
use App\Models\DailyTestMeetings;
use App\Models\DailyTestScores;
use App\Models\MapelSettings;
use App\Models\NilaiMapel;
use App\Models\Settings;
use App\Models\SettingsWaliKelas;
use App\Models\Students;
use App\Models\StudentWaliKelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalOrtuTest extends TestCase
{
    use RefreshDatabase;

    private function setupStudentData(): StudentWaliKelas
    {
        $academicYear = AcademicYear::factory()->create();
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
        ]);
        $settingsWaliKelas = SettingsWaliKelas::factory()->create([
            'academicYear_id' => $academicYear->id,
        ]);
        $mapel = MapelSettings::factory()->create([
            'settingsWaliKelas_id' => $settingsWaliKelas->id,
            'mapel' => 'Matematika',
            'kkm' => 75,
        ]);

        /** @var StudentWaliKelas $student */
        $student = StudentWaliKelas::factory()->create([
            'class_id' => $classWaliKelas->id,
            'name' => 'Budi Santoso',
            'nis' => '12345',
            'nisn' => '0098765432',
        ]);

        // Create absensi
        Absensi::factory()->create([
            'student_id' => $student->id,
            'hadir' => 20,
            'izin' => 1,
            'sakit' => 2,
            'alpa' => 0,
        ]);

        // Create nilai mapel
        NilaiMapel::factory()->create([
            'student_id' => $student->id,
            'mapel_id' => $mapel->id,
            'nilai' => 85,
            'capaian' => 'Sangat baik dalam memahami materi Aljabar.',
        ]);

        return $student;
    }

    private function setupMapelStudentData(): Students
    {
        $academicYear = AcademicYear::factory()->create();
        $class = Classes::factory()->create([
            'academic_year_id' => $academicYear->id,
            'name' => 'VII A',
        ]);

        /** @var Students $student */
        $student = Students::factory()->create([
            'class_id' => $class->id,
            'name' => 'Ahmad Fauzi',
            'nis' => '123456',
            'nisn' => '01234567',
        ]);

        // Create subject settings
        Settings::factory()->create([
            'subject_name' => 'IPA',
            'minimum_score' => 70,
        ]);

        // Create daily test meeting and score
        $meeting = DailyTestMeetings::factory()->create([
            'class_id' => $class->id,
            'title' => 'UH 1 Sistem Organ',
            'test_date' => '2026-08-15',
        ]);

        DailyTestScores::factory()->create([
            'daily_test_meeting_id' => $meeting->id,
            'student_id' => $student->id,
            'score' => 80,
        ]);

        // Create attendance
        $attMeeting = AttendanceMeetings::factory()->create([
            'class_id' => $class->id,
        ]);

        Attendances::factory()->create([
            'attendance_meeting_id' => $attMeeting->id,
            'student_id' => $student->id,
            'status' => 'HADIR',
        ]);

        return $student;
    }

    /**
     * Test guest can view parent portal search page.
     */
    public function test_parent_can_view_search_page(): void
    {
        $response = $this->get('/portal-ortu');

        $response->assertStatus(200);
        $response->assertSee('Portal Orang Tua');
        $response->assertSee('Masukkan Nama, NIS, dan NISN siswa untuk melihat data perkembangan belajar.');
    }

    /**
     * Test parent can search with valid student details and be redirected to dashboard.
     */
    public function test_parent_can_search_with_valid_details_and_redirect_to_dashboard(): void
    {
        $student = $this->setupStudentData();

        $response = $this->post('/portal-ortu', [
            'name' => 'Budi Santoso',
            'nis' => '12345',
            'nisn' => '0098765432',
        ]);

        $response->assertRedirect('/portal-ortu/dashboard');
        $response->assertSessionHas('portal_student_id', $student->id);
        $response->assertSessionHas('portal_student_type', 'wali_kelas');
    }

    /**
     * Test parent can search with valid mapel student details.
     */
    public function test_parent_can_search_with_valid_mapel_student_details(): void
    {
        $student = $this->setupMapelStudentData();

        $response = $this->post('/portal-ortu', [
            'name' => 'Ahmad Fauzi',
            'nis' => '123456',
            'nisn' => '01234567',
        ]);

        $response->assertRedirect('/portal-ortu/dashboard');
        $response->assertSessionHas('portal_student_id', $student->id);
        $response->assertSessionHas('portal_student_type', 'mapel');
    }

    /**
     * Test search is case insensitive for name and tolerant to trailing spaces.
     */
    public function test_parent_search_is_case_insensitive_and_space_tolerant(): void
    {
        $student = $this->setupStudentData();

        $response = $this->post('/portal-ortu', [
            'name' => '  budi santoso  ',
            'nis' => '12345',
            'nisn' => '0098765432',
        ]);

        $response->assertRedirect('/portal-ortu/dashboard');
        $response->assertSessionHas('portal_student_id', $student->id);
    }

    /**
     * Test search fails with wrong name, nis, or nisn.
     */
    public function test_parent_cannot_search_with_invalid_details(): void
    {
        $this->setupStudentData();

        // Wrong Name
        $response1 = $this->post('/portal-ortu', [
            'name' => 'Budi Junaidi',
            'nis' => '12345',
            'nisn' => '0098765432',
        ]);
        $response1->assertSessionHasErrors('search_error');

        // Wrong NIS
        $response2 = $this->post('/portal-ortu', [
            'name' => 'Budi Santoso',
            'nis' => '99999',
            'nisn' => '0098765432',
        ]);
        $response2->assertSessionHasErrors('search_error');

        // Wrong NISN
        $response3 = $this->post('/portal-ortu', [
            'name' => 'Budi Santoso',
            'nis' => '12345',
            'nisn' => '9999999999',
        ]);
        $response3->assertSessionHasErrors('search_error');
    }

    /**
     * Test accessing parent portal dashboard requires portal session.
     */
    public function test_parent_dashboard_requires_portal_session(): void
    {
        $response = $this->get('/portal-ortu/dashboard');

        $response->assertRedirect('/portal-ortu');
    }

    /**
     * Test parent dashboard displays correct student data, grades, and attendance.
     */
    public function test_parent_dashboard_displays_student_data(): void
    {
        $student = $this->setupStudentData();

        $response = $this->withSession([
            'portal_student_id' => $student->id,
            'portal_student_type' => 'wali_kelas',
        ])->get('/portal-ortu/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso');
        $response->assertSee('12345');
        $response->assertSee('0098765432');
        $response->assertSee('Matematika');
        $response->assertSee('85');
        $response->assertSee('Sangat baik dalam memahami materi Aljabar.');
        $response->assertSee('Kehadiran');
    }

    /**
     * Test parent dashboard displays mapel student details and dynamic scores correctly.
     */
    public function test_parent_dashboard_displays_mapel_student_data(): void
    {
        $student = $this->setupMapelStudentData();

        $response = $this->withSession([
            'portal_student_id' => $student->id,
            'portal_student_type' => 'mapel',
        ])->get('/portal-ortu/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Ahmad Fauzi');
        $response->assertSee('123456');
        $response->assertSee('01234567');
        $response->assertSee('UH 1 Sistem Organ');
        $response->assertSee('80');
        $response->assertSee('Tuntas');
        $response->assertSee('Kehadiran');
    }

    /**
     * Test parent can exit the portal.
     */
    public function test_parent_can_exit_portal(): void
    {
        $student = $this->setupStudentData();

        $response = $this->withSession([
            'portal_student_id' => $student->id,
            'portal_student_type' => 'wali_kelas',
        ])->post('/portal-ortu/exit');

        $response->assertRedirect('/portal-ortu');
        $this->assertNull(session('portal_student_id'));
        $this->assertNull(session('portal_student_type'));
    }
}
