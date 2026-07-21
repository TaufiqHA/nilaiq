<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\StudentWaliKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaliKelasDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest cannot access wali kelas dashboard.
     */
    public function test_guest_cannot_access_wali_kelas_dashboard(): void
    {
        $response = $this->get(route('wali-kelas.dashboard'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test non-wali_kelas user cannot access wali kelas dashboard.
     */
    public function test_non_wali_kelas_user_cannot_access_wali_kelas_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'mapel']);

        $response = $this->actingAs($user)->get(route('wali-kelas.dashboard'));

        $response->assertStatus(403);
    }

    /**
     * Test authenticated wali_kelas user can view dashboard with student_wali_kelas data.
     */
    public function test_wali_kelas_user_can_view_dashboard_with_student_data(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
            'name' => 'Kelas X MIPA 1',
        ]);

        $student1 = StudentWaliKelas::factory()->create([
            'class_id' => $classWaliKelas->id,
            'name' => 'Ahmad Fadhil',
            'gender' => 'L',
            'status' => 'ACTIVE',
        ]);

        $student2 = StudentWaliKelas::factory()->create([
            'class_id' => $classWaliKelas->id,
            'name' => 'Siti Aminah',
            'gender' => 'P',
            'status' => 'ACTIVE',
        ]);

        $response = $this->actingAs($user)->get(route('wali-kelas.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalStudents', 2);
        $response->assertViewHas('maleStudentsCount', 1);
        $response->assertViewHas('femaleStudentsCount', 1);
        $response->assertSee('Ahmad Fadhil');
        $response->assertSee('Siti Aminah');
        $response->assertSee('Kelas X MIPA 1');
    }
}
