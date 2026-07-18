<?php

namespace Tests\Feature;

use App\Models\Classes;
use App\Models\Students;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_students(): void
    {
        $student = Students::factory()->create();

        $this->get(route('students.index'))->assertRedirect(route('login'));
        $this->get(route('students.show', $student))->assertRedirect(route('login'));
        $this->post(route('students.store'), [])->assertRedirect(route('login'));
        $this->put(route('students.update', $student), [])->assertRedirect(route('login'));
        $this->delete(route('students.destroy', $student))->assertRedirect(route('login'));
        $this->delete(route('students.delete', $student))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access index.
     */
    public function test_authenticated_user_can_access_students_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Students::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('students.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Test authenticated user can store student.
     */
    public function test_authenticated_user_can_store_student(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $class = Classes::factory()->create();

        $studentData = [
            'class_id' => $class->id,
            'nis' => '1234567890',
            'nisn' => '0987654321',
            'name' => 'John Doe',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-05-15',
            'address' => 'Jl. Merdeka No. 10',
            'parent_name' => 'Jane Doe',
            'parent_phone' => '08123456789',
            'status' => 'ACTIVE',
        ];

        $response = $this->actingAs($user)->postJson(route('students.store'), $studentData);

        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Student created successfully.');
        $response->assertJsonPath('data.name', 'John Doe');

        $this->assertDatabaseHas('students', [
            'nis' => '1234567890',
            'nisn' => '0987654321',
            'name' => 'John Doe',
        ]);
    }

    /**
     * Test validation rules on store.
     */
    public function test_store_student_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Test required fields
        $response = $this->actingAs($user)->post(route('students.store'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'class_id', 'nis', 'nisn', 'name', 'gender',
            'birth_place', 'birth_date', 'address',
            'parent_name', 'parent_phone', 'status',
        ]);

        // Test invalid enum and data values
        $response = $this->actingAs($user)->post(route('students.store'), [
            'class_id' => 9999,
            'gender' => 'X',
            'status' => 'INVALID_STATUS',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['class_id', 'gender', 'status']);
    }

    /**
     * Test unique nis and nisn validation.
     */
    public function test_unique_nis_and_nisn_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $existingStudent = Students::factory()->create([
            'nis' => '1111111111',
            'nisn' => '2222222222',
        ]);
        $newClass = Classes::factory()->create();

        $response = $this->actingAs($user)->post(route('students.store'), [
            'class_id' => $newClass->id,
            'nis' => '1111111111',
            'nisn' => '2222222222',
            'name' => 'Duplicate Test',
            'gender' => 'P',
            'birth_place' => 'Surabaya',
            'birth_date' => '2011-06-20',
            'address' => 'Jl. Bubutan No. 5',
            'parent_name' => 'Slamet',
            'parent_phone' => '0876543210',
            'status' => 'ACTIVE',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['nis', 'nisn']);
    }

    /**
     * Test authenticated user can view details.
     */
    public function test_authenticated_user_can_view_student_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $student = Students::factory()->create();

        $response = $this->actingAs($user)->getJson(route('students.show', $student));

        $response->assertStatus(200);
        $response->assertJsonPath('id', $student->id);
        $response->assertJsonPath('name', $student->name);
    }

    /**
     * Test authenticated user can update student.
     */
    public function test_authenticated_user_can_update_student(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $student = Students::factory()->create([
            'name' => 'Old Name',
            'nis' => '9999999999',
            'nisn' => '8888888888',
        ]);
        $newClass = Classes::factory()->create();

        $updateData = [
            'class_id' => $newClass->id,
            'nis' => '9999999999', // Keeping the same is allowed
            'nisn' => '8888888888', // Keeping the same is allowed
            'name' => 'New Name',
            'gender' => $student->gender,
            'birth_place' => $student->birth_place,
            'birth_date' => $student->birth_date->format('Y-m-d'),
            'address' => 'New Address',
            'parent_name' => $student->parent_name,
            'parent_phone' => $student->parent_phone,
            'status' => 'INACTIVE',
        ];

        $response = $this->actingAs($user)->putJson(route('students.update', $student), $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Student updated successfully.');
        $response->assertJsonPath('data.name', 'New Name');
        $response->assertJsonPath('data.status', 'INACTIVE');

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'New Name',
            'address' => 'New Address',
            'status' => 'INACTIVE',
        ]);
    }

    /**
     * Test authenticated user can destroy student.
     */
    public function test_authenticated_user_can_delete_student_via_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $student = Students::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('students.destroy', $student));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Student deleted successfully.');

        $this->assertDatabaseMissing('students', [
            'id' => $student->id,
        ]);
    }

    /**
     * Test authenticated user can delete via delete route.
     */
    public function test_authenticated_user_can_delete_student_via_delete_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $student = Students::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('students.delete', $student));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Student deleted successfully.');

        $this->assertDatabaseMissing('students', [
            'id' => $student->id,
        ]);
    }
}
