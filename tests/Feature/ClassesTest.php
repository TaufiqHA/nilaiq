<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_classes(): void
    {
        $class = Classes::factory()->create();

        $this->get(route('classes.index'))->assertRedirect(route('login'));
        $this->get(route('classes.show', $class))->assertRedirect(route('login'));
        $this->post(route('classes.store'), [])->assertRedirect(route('login'));
        $this->put(route('classes.update', $class), [])->assertRedirect(route('login'));
        $this->delete(route('classes.destroy', $class))->assertRedirect(route('login'));
        $this->delete(route('classes.delete', $class))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access index.
     */
    public function test_authenticated_user_can_access_classes_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Classes::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('classes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.Classes');
        $response->assertViewHas('classes');
        $response->assertViewHas('academicYears');
    }

    /**
     * Test authenticated user can access index JSON.
     */
    public function test_authenticated_user_can_access_classes_index_json(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $class = Classes::factory()->create();

        $response = $this->actingAs($user)->getJson(route('classes.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $class->id,
            'name' => $class->name,
            'academic_year_id' => $class->academic_year_id,
        ]);
    }

    /**
     * Test authenticated user can store class.
     */
    public function test_authenticated_user_can_store_class(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $academicYear = AcademicYear::factory()->create();
        $classData = [
            'academic_year_id' => $academicYear->id,
            'name' => 'Kelas X MIPA 1',
        ];

        $response = $this->actingAs($user)->post(route('classes.store'), $classData);

        $this->assertDatabaseHas('classes', [
            'academic_year_id' => $academicYear->id,
            'name' => 'Kelas X MIPA 1',
        ]);

        $response->assertRedirect(route('classes.index'));
        $response->assertSessionHas('success', 'Class created successfully.');
    }

    /**
     * Test validation rules on store.
     */
    public function test_store_class_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Test required fields
        $response = $this->actingAs($user)->post(route('classes.store'), []);
        $response->assertSessionHasErrors(['academic_year_id', 'name']);

        // Test invalid academic_year_id
        $response = $this->actingAs($user)->post(route('classes.store'), [
            'academic_year_id' => 9999,
            'name' => 'Valid Name',
        ]);
        $response->assertSessionHasErrors(['academic_year_id']);
    }

    /**
     * Test authenticated user can view details.
     */
    public function test_authenticated_user_can_view_class_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $class = Classes::factory()->create();

        $response = $this->actingAs($user)->get(route('classes.show', $class));

        $response->assertStatus(200);
        $response->assertViewIs('auth.Classes');
        $response->assertViewHas('class');
    }

    /**
     * Test authenticated user can update class.
     */
    public function test_authenticated_user_can_update_class(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $class = Classes::factory()->create();
        $academicYear = AcademicYear::factory()->create();

        $updateData = [
            'academic_year_id' => $academicYear->id,
            'name' => 'Kelas XI IPS 2',
        ];

        $response = $this->actingAs($user)->put(route('classes.update', $class), $updateData);

        $this->assertDatabaseHas('classes', [
            'id' => $class->id,
            'academic_year_id' => $academicYear->id,
            'name' => 'Kelas XI IPS 2',
        ]);

        $response->assertRedirect(route('classes.index'));
        $response->assertSessionHas('success', 'Class updated successfully.');
    }

    /**
     * Test authenticated user can destroy class.
     */
    public function test_authenticated_user_can_delete_class_via_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $class = Classes::factory()->create();

        $response = $this->actingAs($user)->delete(route('classes.destroy', $class));

        $this->assertDatabaseMissing('classes', [
            'id' => $class->id,
        ]);

        $response->assertRedirect(route('classes.index'));
        $response->assertSessionHas('success', 'Class deleted successfully.');
    }

    /**
     * Test authenticated user can delete via delete route.
     */
    public function test_authenticated_user_can_delete_class_via_delete_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $class = Classes::factory()->create();

        $response = $this->actingAs($user)->delete(route('classes.delete', $class));

        $this->assertDatabaseMissing('classes', [
            'id' => $class->id,
        ]);

        $response->assertRedirect(route('classes.index'));
        $response->assertSessionHas('success', 'Class deleted successfully.');
    }
}
