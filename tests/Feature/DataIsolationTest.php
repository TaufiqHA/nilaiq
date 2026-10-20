<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\AttendanceMeetings;
use App\Models\Attendances;
use App\Models\Classes;
use App\Models\ClassWaliKelas;
use App\Models\DailyTestMeetings;
use App\Models\DailyTestScores;
use App\Models\Recaps;
use App\Models\Settings;
use App\Models\SettingsWaliKelas;
use App\Models\Students;
use App\Models\StudentWaliKelas;
use App\Models\User;
use App\Services\LegacySettingsConsolidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DataIsolationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test mapel lists only return records owned by the authenticated user.
     */
    public function test_mapel_lists_are_isolated_per_user(): void
    {
        [$yearA, $classA, $studentA] = $this->mapelData();
        /** @var User $userB */
        $userB = User::factory()->create(['role' => 'mapel']);

        $this->actingAs($userB)->getJson(route('classes.index'))
            ->assertOk()
            ->assertJsonCount(0);

        $this->actingAs($userB)->getJson(route('students.index'))
            ->assertOk()
            ->assertJsonCount(0);

        $this->actingAs($userA = $this->lastMapelUser($yearA))->getJson(route('classes.index'))
            ->assertOk()
            ->assertJsonCount(1);

        $this->actingAs($userA)->getJson(route('students.index'))
            ->assertOk()
            ->assertJsonCount(1);
    }

    /**
     * Test direct access to another user's mapel records is denied.
     */
    public function test_mapel_user_cannot_access_other_users_records(): void
    {
        [$yearA, $classA, $studentA] = $this->mapelData();
        /** @var User $userB */
        $userB = User::factory()->create(['role' => 'mapel']);

        $this->actingAs($userB)->getJson(route('classes.show', $classA))->assertNotFound();
        $this->actingAs($userB)->putJson(route('classes.update', $classA), [
            'academic_year_id' => $yearA->id,
            'name' => 'Kelas Bajakan',
        ])->assertNotFound();
        $this->actingAs($userB)->deleteJson(route('classes.destroy', $classA))->assertNotFound();

        $this->actingAs($userB)->getJson(route('students.show', $studentA))->assertNotFound();
        $this->actingAs($userB)->deleteJson(route('students.destroy', $studentA))->assertNotFound();

        $this->assertModelExists($classA);
        $this->assertModelExists($studentA);
    }

    /**
     * Test a user cannot attach child records to another user's parent.
     */
    public function test_mapel_user_cannot_store_under_another_users_parent(): void
    {
        [$yearA, $classA] = $this->mapelData();
        /** @var User $userB */
        $userB = User::factory()->create(['role' => 'mapel']);

        $this->actingAs($userB)->postJson(route('classes.store'), [
            'academic_year_id' => $yearA->id,
            'name' => 'Kelas Curian',
        ])->assertStatus(422)->assertJsonValidationErrors('academic_year_id');

        $this->actingAs($userB)->postJson(route('attendance-meetings.store'), [
            'class_id' => $classA->id,
            'title' => 'Pertemuan Curian',
            'meeting_date' => now()->toDateString(),
            'tipe' => 'harian',
        ])->assertStatus(422)->assertJsonValidationErrors('class_id');

        $this->assertSame(0, Classes::query()->where('name', 'Kelas Curian')->count());
        $this->assertSame(0, AttendanceMeetings::query()->where('title', 'Pertemuan Curian')->count());
    }

    /**
     * Test meetings, attendances, and scores are isolated between mapel users.
     */
    public function test_mapel_meetings_and_scores_are_isolated(): void
    {
        [$yearA, $classA, $studentA] = $this->mapelData();
        $meeting = AttendanceMeetings::factory()->create(['class_id' => $classA->id]);
        $attendance = Attendances::factory()->create([
            'attendance_meeting_id' => $meeting->id,
            'student_id' => $studentA->id,
        ]);
        $dailyTest = DailyTestMeetings::factory()->create(['class_id' => $classA->id]);
        $score = DailyTestScores::factory()->create([
            'daily_test_meeting_id' => $dailyTest->id,
            'student_id' => $studentA->id,
        ]);

        /** @var User $userB */
        $userB = User::factory()->create(['role' => 'mapel']);

        $this->actingAs($userB)->getJson(route('attendance-meetings.index'))->assertJsonCount(0);
        $this->actingAs($userB)->getJson(route('attendances.index'))->assertJsonCount(0);
        $this->actingAs($userB)->getJson(route('daily-test-meetings.index'))->assertJsonCount(0);
        $this->actingAs($userB)->getJson(route('daily-test-scores.index'))->assertJsonCount(0);

        $this->actingAs($userB)->getJson(route('attendance-meetings.show', $meeting))->assertNotFound();
        $this->actingAs($userB)->getJson(route('attendances.show', $attendance))->assertNotFound();
        $this->actingAs($userB)->getJson(route('daily-test-scores.show', $score))->assertNotFound();

        $this->actingAs($userB)->postJson(route('attendances.store'), [
            'attendance_meeting_id' => $meeting->id,
            'student_id' => $studentA->id,
            'status' => 'HADIR',
        ])->assertStatus(422);
    }

    /**
     * Test recaps are isolated between mapel users.
     */
    public function test_mapel_recaps_are_isolated(): void
    {
        [$yearA, $classA, $studentA] = $this->mapelData();
        $recap = Recaps::factory()->create([
            'academic_year_id' => $yearA->id,
            'class_id' => $classA->id,
            'student_id' => $studentA->id,
        ]);

        /** @var User $userB */
        $userB = User::factory()->create(['role' => 'mapel']);

        $this->actingAs($userB)->getJson(route('recaps.index'))->assertJsonCount(0);
        $this->actingAs($userB)->getJson(route('recaps.show', $recap))->assertNotFound();
        $this->actingAs($userB)->deleteJson(route('recaps.destroy', $recap))->assertNotFound();
    }

    /**
     * Test wali kelas students and settings are isolated per user.
     */
    public function test_wali_kelas_data_is_isolated(): void
    {
        [$waliA, $classA, $studentA] = $this->waliData('Kelas A');
        [$waliB, $classB, $studentB] = $this->waliData('Kelas B');

        $this->actingAs($waliB)
            ->getJson(route('wali-kelas.student-wali-kelas.index'))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $studentB->id])
            ->assertJsonMissing(['id' => $studentA->id]);

        $this->actingAs($waliB)
            ->getJson(route('wali-kelas.student-wali-kelas.show', $studentA))
            ->assertNotFound();

        $this->actingAs($waliB)
            ->putJson(route('wali-kelas.student-wali-kelas.update', $studentA), [
                'class_id' => $classB->id,
                'name' => 'Nama Curian',
            ])->assertNotFound();

        $this->actingAs($waliB)->postJson(route('wali-kelas.student-wali-kelas.store'), [
            'class_id' => $classA->id,
            'nis' => '9999999999',
            'nisn' => '8888888888',
            'name' => 'Siswa Curian',
            'gender' => 'L',
            'birth_place' => 'Bandung',
            'address' => 'Jalan',
            'status' => 'ACTIVE',
        ])->assertStatus(422)->assertJsonValidationErrors('class_id');

        $settingsA = SettingsWaliKelas::factory()->create(['academicYear_id' => $classA->academic_year_id]);
        $settingsB = SettingsWaliKelas::factory()->create(['academicYear_id' => $classB->academic_year_id]);

        $this->actingAs($waliB)->getJson(route('settings-wali-kelas.index'))
            ->assertOk()
            ->assertJsonFragment(['id' => $settingsB->id])
            ->assertJsonMissing(['id' => $settingsA->id]);
    }

    /**
     * Test the wali kelas master data view never renders another user's data.
     */
    public function test_wali_master_data_view_is_isolated(): void
    {
        [$waliA, $classA] = $this->waliData('Kelas A');
        [$waliB, $classB] = $this->waliData('Kelas B');

        SettingsWaliKelas::factory()->create([
            'academicYear_id' => $classA->academic_year_id,
            'school_name' => 'Sekolah Rahasia Wali A',
        ]);
        SettingsWaliKelas::factory()->create([
            'academicYear_id' => $classB->academic_year_id,
            'school_name' => 'Sekolah Wali B',
        ]);

        $this->actingAs($waliB)
            ->get(route('settings-wali-kelas.index'))
            ->assertOk()
            ->assertSee('Sekolah Wali B')
            ->assertDontSee('Sekolah Rahasia Wali A');
    }

    /**
     * Test the mapel master data view never renders another user's data.
     */
    public function test_mapel_master_data_view_is_isolated(): void
    {
        /** @var User $userA */
        $userA = User::factory()->create(['role' => 'mapel']);
        /** @var User $userB */
        $userB = User::factory()->create(['role' => 'mapel']);

        Settings::factory()->create([
            'user_id' => $userA->id,
            'school_name' => 'Sekolah Rahasia Mapel A',
        ]);

        $this->actingAs($userB)
            ->get(route('master-data.index'))
            ->assertOk()
            ->assertDontSee('Sekolah Rahasia Mapel A');

        $this->actingAs($userA)
            ->get(route('master-data.index'))
            ->assertOk()
            ->assertSee('Sekolah Rahasia Mapel A');
    }

    /**
     * Test master data settings are isolated per mapel user.
     */
    public function test_mapel_settings_are_isolated(): void
    {
        /** @var User $userA */
        $userA = User::factory()->create(['role' => 'mapel']);
        /** @var User $userB */
        $userB = User::factory()->create(['role' => 'mapel']);

        $settingsA = Settings::factory()->create([
            'user_id' => $userA->id,
            'school_name' => 'Sekolah Milik A',
        ]);

        $this->actingAs($userB)->getJson(route('settings.index'))
            ->assertOk()
            ->assertJsonMissing(['id' => $settingsA->id])
            ->assertJsonMissing(['school_name' => 'Sekolah Milik A']);

        $this->actingAs($userA)->getJson(route('settings.index'))
            ->assertOk()
            ->assertJsonFragment(['id' => $settingsA->id]);
    }

    /**
     * Test legacy global settings are kept only on the oldest account.
     */
    public function test_legacy_settings_are_consolidated_to_the_oldest_account(): void
    {
        /** @var User $oldest */
        $oldest = User::factory()->create(['role' => 'mapel']);
        /** @var User $newer */
        $newer = User::factory()->create(['role' => 'mapel']);

        $attributes = [
            'school_name' => 'Sekolah Lama',
            'npsn' => '12345678',
            'teacher_name' => 'Guru Lama',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('settings')->insert([
            ...$attributes,
            'user_id' => null,
        ]);
        DB::table('settings')->insert(['user_id' => $oldest->id, ...$attributes]);
        DB::table('settings')->insert(['user_id' => $newer->id, ...$attributes]);

        app(LegacySettingsConsolidator::class)->consolidate();

        $this->assertDatabaseHas('settings', [
            'user_id' => $oldest->id,
            'school_name' => 'Sekolah Lama',
        ]);
        $this->assertDatabaseMissing('settings', ['user_id' => $newer->id]);
        $this->assertDatabaseMissing('settings', ['user_id' => null]);
    }

    /**
     * Test consolidation keeps a custom setting on the oldest account.
     */
    public function test_consolidation_preserves_edited_settings(): void
    {
        /** @var User $oldest */
        $oldest = User::factory()->create(['role' => 'mapel']);
        /** @var User $newer */
        $newer = User::factory()->create(['role' => 'mapel']);

        $legacy = [
            'school_name' => 'Sekolah Global',
            'npsn' => '99999999',
            'teacher_name' => 'Guru Global',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('settings')->insert(['user_id' => null, ...$legacy]);
        Settings::factory()->create([
            'user_id' => $oldest->id,
            'school_name' => 'Sekolah Pilihan Sendiri',
        ]);
        DB::table('settings')->insert(['user_id' => $newer->id, ...$legacy]);

        app(LegacySettingsConsolidator::class)->consolidate();

        $this->assertDatabaseHas('settings', [
            'user_id' => $oldest->id,
            'school_name' => 'Sekolah Pilihan Sendiri',
        ]);
        $this->assertDatabaseMissing('settings', ['user_id' => $newer->id]);
        $this->assertDatabaseMissing('settings', ['user_id' => null]);
    }

    /**
     * Test a global legacy row is never rendered for a new account.
     */
    public function test_global_legacy_settings_are_not_rendered_to_new_users(): void
    {
        DB::table('settings')->insert([
            'user_id' => null,
            'school_name' => 'Sekolah Global Lama',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);

        $this->actingAs($user)
            ->get(route('master-data.index'))
            ->assertOk()
            ->assertDontSee('Sekolah Global Lama');
    }

    /**
     * Test active academic year never leaks across users.
     */
    public function test_active_academic_year_is_never_shared(): void
    {
        /** @var User $userA */
        $userA = User::factory()->create(['role' => 'mapel']);
        /** @var User $userB */
        $userB = User::factory()->create(['role' => 'mapel']);

        AcademicYear::factory()->active()->create(['user_id' => $userA->id]);

        $this->assertNull(AcademicYear::getActive($userB->id));
        $this->assertNotNull(AcademicYear::getActive($userA->id));
    }

    /**
     * Create a mapel data set owned by a new user.
     *
     * @return array{0: AcademicYear, 1: Classes, 2: Students}
     */
    private function mapelData(): array
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);
        $year = AcademicYear::factory()->create(['user_id' => $user->id]);
        $class = Classes::factory()->create(['academic_year_id' => $year->id]);
        $student = Students::factory()->create(['class_id' => $class->id]);

        return [$year, $class, $student];
    }

    /**
     * Resolve the user that owns the given academic year.
     */
    private function lastMapelUser(AcademicYear $year): User
    {
        return User::query()->findOrFail($year->user_id);
    }

    /**
     * Create a wali kelas data set owned by a new user.
     *
     * @return array{0: User, 1: ClassWaliKelas, 2: StudentWaliKelas}
     */
    private function waliData(string $className): array
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $year = AcademicYear::factory()->create(['user_id' => $user->id]);
        $class = ClassWaliKelas::factory()->create([
            'academic_year_id' => $year->id,
            'user_id' => $user->id,
            'name' => $className,
        ]);
        $student = StudentWaliKelas::factory()->create(['class_id' => $class->id]);

        return [$user, $class, $student];
    }
}
