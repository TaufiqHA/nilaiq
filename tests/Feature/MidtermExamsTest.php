<?php

namespace Tests\Feature;

use App\Models\Classes;
use App\Models\MidtermExams;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtermExamsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_midterm_exams(): void
    {
        $exam = MidtermExams::factory()->create();

        $this->get(route('midterm-exams.index'))->assertRedirect(route('login'));
        $this->get(route('midterm-exams.show', $exam))->assertRedirect(route('login'));
        $this->post(route('midterm-exams.store'), [])->assertRedirect(route('login'));
        $this->put(route('midterm-exams.update', $exam), [])->assertRedirect(route('login'));
        $this->delete(route('midterm-exams.destroy', $exam))->assertRedirect(route('login'));
        $this->delete(route('midterm-exams.delete', $exam))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access index.
     */
    public function test_authenticated_user_can_access_midterm_exams_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        MidtermExams::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('midterm-exams.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Test authenticated user can store midterm exam.
     */
    public function test_authenticated_user_can_store_midterm_exam(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $class = Classes::factory()->create();

        $examData = [
            'class_id' => $class->id,
            'title' => 'UTS Matematika',
            'exam_date' => '2026-07-18',
            'description' => 'Ujian Tengah Semester Ganjil.',
        ];

        $response = $this->actingAs($user)->postJson(route('midterm-exams.store'), $examData);

        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Midterm exam created successfully.');
        $response->assertJsonPath('data.title', 'UTS Matematika');

        $this->assertDatabaseHas('midterm_exams', [
            'class_id' => $class->id,
            'title' => 'UTS Matematika',
            'description' => 'Ujian Tengah Semester Ganjil.',
        ]);

        $storedExam = MidtermExams::first();
        $this->assertEquals('2026-07-18', $storedExam->exam_date->format('Y-m-d'));
    }

    /**
     * Test validation rules on store.
     */
    public function test_store_midterm_exam_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Test required fields
        $response = $this->actingAs($user)->post(route('midterm-exams.store'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'class_id', 'title', 'exam_date',
        ]);

        // Test invalid class_id
        $response = $this->actingAs($user)->post(route('midterm-exams.store'), [
            'class_id' => 9999,
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['class_id']);
    }

    /**
     * Test authenticated user can view details.
     */
    public function test_authenticated_user_can_view_midterm_exam_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $exam = MidtermExams::factory()->create();

        $response = $this->actingAs($user)->getJson(route('midterm-exams.show', $exam));

        $response->assertStatus(200);
        $response->assertJsonPath('id', $exam->id);
        $response->assertJsonPath('title', $exam->title);
    }

    /**
     * Test authenticated user can update midterm exam.
     */
    public function test_authenticated_user_can_update_midterm_exam(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $exam = MidtermExams::factory()->create([
            'title' => 'UTS Lama',
            'description' => 'Deskripsi lama.',
        ]);
        $newClass = Classes::factory()->create();

        $updateData = [
            'class_id' => $newClass->id,
            'title' => 'UTS Baru',
            'exam_date' => '2026-07-19',
            'description' => 'Deskripsi baru.',
        ];

        $response = $this->actingAs($user)->putJson(route('midterm-exams.update', $exam), $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Midterm exam updated successfully.');
        $response->assertJsonPath('data.title', 'UTS Baru');
        $response->assertJsonPath('data.description', 'Deskripsi baru.');

        $this->assertDatabaseHas('midterm_exams', [
            'id' => $exam->id,
            'class_id' => $newClass->id,
            'title' => 'UTS Baru',
            'description' => 'Deskripsi baru.',
        ]);

        $this->assertEquals('2026-07-19', $exam->refresh()->exam_date->format('Y-m-d'));
    }

    /**
     * Test authenticated user can destroy midterm exam.
     */
    public function test_authenticated_user_can_delete_midterm_exam_via_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $exam = MidtermExams::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('midterm-exams.destroy', $exam));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Midterm exam deleted successfully.');

        $this->assertDatabaseMissing('midterm_exams', [
            'id' => $exam->id,
        ]);
    }

    /**
     * Test authenticated user can delete via delete route.
     */
    public function test_authenticated_user_can_delete_midterm_exam_via_delete_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $exam = MidtermExams::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('midterm-exams.delete', $exam));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Midterm exam deleted successfully.');

        $this->assertDatabaseMissing('midterm_exams', [
            'id' => $exam->id,
        ]);
    }
}
