<?php

namespace Tests\Unit;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\Ekskul;
use App\Models\StudentWaliKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EkskulTest extends TestCase
{
    use RefreshDatabase;

    private function createStudent(): StudentWaliKelas
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);

        return StudentWaliKelas::factory()->create(['class_id' => $classWaliKelas->id]);
    }

    /**
     * Test creating an ekskul record via factory and check attributes.
     */
    public function test_ekskul_can_be_created_with_factory(): void
    {
        $student = $this->createStudent();

        $ekskul = Ekskul::factory()->create([
            'student_id' => $student->id,
            'ekskul1' => 'Pramuka',
            'ekskul2' => 'Paskibra',
            'ekskul3' => 'PMR',
        ]);

        $this->assertDatabaseHas('ekskuls', [
            'id' => $ekskul->id,
            'student_id' => $student->id,
            'ekskul1' => 'Pramuka',
            'ekskul2' => 'Paskibra',
            'ekskul3' => 'PMR',
        ]);
    }

    /**
     * Test ekskul belongs to student_wali_kelas relationship.
     */
    public function test_ekskul_belongs_to_student(): void
    {
        $student = $this->createStudent();
        $ekskul = Ekskul::factory()->create(['student_id' => $student->id]);

        $this->assertInstanceOf(StudentWaliKelas::class, $ekskul->student);
        $this->assertEquals($student->id, $ekskul->student->id);
    }

    /**
     * Test student_wali_kelas has one ekskul relationship.
     */
    public function test_student_has_one_ekskul(): void
    {
        $student = $this->createStudent();
        $ekskul = Ekskul::factory()->create(['student_id' => $student->id]);

        $this->assertInstanceOf(Ekskul::class, $student->ekskul);
        $this->assertEquals($ekskul->id, $student->ekskul->id);
    }
}
