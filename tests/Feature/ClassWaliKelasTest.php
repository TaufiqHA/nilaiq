<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassWaliKelas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassWaliKelasTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests cannot access class wali kelas routes.
     */
    public function test_guest_cannot_access_class_wali_kelas(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);

        $this->getJson(route('wali-kelas.class-wali-kelas.index'))->assertStatus(401);
        $this->getJson(route('wali-kelas.class-wali-kelas.show', $classWaliKelas))->assertStatus(401);
        $this->postJson(route('wali-kelas.class-wali-kelas.store'), [])->assertStatus(401);
        $this->putJson(route('wali-kelas.class-wali-kelas.update', $classWaliKelas), [])->assertStatus(401);
        $this->deleteJson(route('wali-kelas.class-wali-kelas.destroy', $classWaliKelas))->assertStatus(401);
        $this->deleteJson(route('wali-kelas.class-wali-kelas.delete', $classWaliKelas))->assertStatus(401);
    }

    /**
     * Test user without wali_kelas role cannot access class wali kelas routes.
     */
    public function test_non_wali_kelas_user_cannot_access_class_wali_kelas(): void
    {
        $user = User::factory()->create(['role' => 'mapel']);
        $waliKelasUser = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $waliKelasUser->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $waliKelasUser->id,
        ]);

        $this->actingAs($user)->getJson(route('wali-kelas.class-wali-kelas.index'))->assertStatus(403);
        $this->actingAs($user)->getJson(route('wali-kelas.class-wali-kelas.show', $classWaliKelas))->assertStatus(403);
        $this->actingAs($user)->postJson(route('wali-kelas.class-wali-kelas.store'), [])->assertStatus(403);
        $this->actingAs($user)->putJson(route('wali-kelas.class-wali-kelas.update', $classWaliKelas), [])->assertStatus(403);
        $this->actingAs($user)->deleteJson(route('wali-kelas.class-wali-kelas.destroy', $classWaliKelas))->assertStatus(403);
        $this->actingAs($user)->deleteJson(route('wali-kelas.class-wali-kelas.delete', $classWaliKelas))->assertStatus(403);
    }

    /**
     * Test authenticated wali_kelas user can access class wali kelas index JSON.
     */
    public function test_wali_kelas_can_access_class_wali_kelas_index_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Kelas X MIPA 1',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.class-wali-kelas.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => 'Kelas X MIPA 1',
        ]);
    }

    /**
     * Test filtering class wali kelas index by academic_year_id and user_id.
     */
    public function test_wali_kelas_can_filter_class_wali_kelas(): void
    {
        $user1 = User::factory()->create(['role' => 'wali_kelas']);
        $user2 = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear1 = AcademicYear::factory()->create(['user_id' => $user1->id]);
        $academicYear2 = AcademicYear::factory()->create(['user_id' => $user2->id]);

        $class1 = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear1->id,
            'name' => 'Kelas X MIPA 1',
            'user_id' => $user1->id,
        ]);
        $class2 = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear2->id,
            'name' => 'Kelas XI IPS 2',
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->getJson(route('wali-kelas.class-wali-kelas.index', [
            'academic_year_id' => $academicYear1->id,
        ]));

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Kelas X MIPA 1']);
        $response->assertJsonMissing(['name' => 'Kelas XI IPS 2']);
    }

    /**
     * Test authenticated wali_kelas user can store class wali kelas via JSON.
     */
    public function test_wali_kelas_can_store_class_wali_kelas_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);

        $data = [
            'academic_year_id' => $academicYear->id,
            'name' => 'Kelas XII MIPA 3',
            'user_id' => $user->id,
        ];

        $response = $this->actingAs($user)->postJson(route('wali-kelas.class-wali-kelas.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'academic_year_id',
                'name',
                'user_id',
                'academic_year',
                'user',
            ],
        ]);

        $this->assertDatabaseHas('class_wali_kelas', [
            'academic_year_id' => $academicYear->id,
            'name' => 'Kelas XII MIPA 3',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can show class wali kelas via JSON.
     */
    public function test_wali_kelas_can_show_class_wali_kelas_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson(route('wali-kelas.class-wali-kelas.show', $classWaliKelas));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $classWaliKelas->id,
            'academic_year_id' => $academicYear->id,
            'name' => $classWaliKelas->name,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can update class wali kelas via JSON.
     */
    public function test_wali_kelas_can_update_class_wali_kelas_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);

        $updatedData = [
            'academic_year_id' => $academicYear->id,
            'name' => 'Kelas X MIPA 1 Updated',
            'user_id' => $user->id,
        ];

        $response = $this->actingAs($user)->putJson(route('wali-kelas.class-wali-kelas.update', $classWaliKelas), $updatedData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data',
        ]);

        $this->assertDatabaseHas('class_wali_kelas', [
            'id' => $classWaliKelas->id,
            'name' => 'Kelas X MIPA 1 Updated',
        ]);
    }

    /**
     * Test authenticated wali_kelas user can destroy class wali kelas via JSON.
     */
    public function test_wali_kelas_can_destroy_class_wali_kelas_json(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.class-wali-kelas.destroy', $classWaliKelas));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Class wali kelas deleted successfully.',
        ]);

        $this->assertDatabaseMissing('class_wali_kelas', [
            'id' => $classWaliKelas->id,
        ]);
    }

    /**
     * Test authenticated wali_kelas user can delete class wali kelas via alias route.
     */
    public function test_wali_kelas_can_delete_class_wali_kelas_via_alias(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);
        $academicYear = AcademicYear::factory()->create(['user_id' => $user->id]);
        $classWaliKelas = ClassWaliKelas::factory()->create([
            'academic_year_id' => $academicYear->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('wali-kelas.class-wali-kelas.delete', $classWaliKelas));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Class wali kelas deleted successfully.',
        ]);

        $this->assertDatabaseMissing('class_wali_kelas', [
            'id' => $classWaliKelas->id,
        ]);
    }

    /**
     * Test class wali kelas validation errors.
     */
    public function test_class_wali_kelas_validation_errors(): void
    {
        $user = User::factory()->create(['role' => 'wali_kelas']);

        $response = $this->actingAs($user)->postJson(route('wali-kelas.class-wali-kelas.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'academic_year_id',
            'name',
            'user_id',
        ]);
    }
}
