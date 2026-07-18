<?php

namespace Tests\Feature;

use App\Models\Classes;
use App\Models\FinalExams;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinalExamsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_final_exams(): void
    {
        $exam = FinalExams::factory()->create();

        $this->get(route('final-exams.index'))->assertRedirect(route('login'));
        $this->get(route('final-exams.show', $exam))->assertRedirect(route('login'));
        $this->post(route('final-exams.store'), [])->assertRedirect(route('login'));
        $this->put(route('final-exams.update', $exam), [])->assertRedirect(route('login'));
        $this->delete(route('final-exams.destroy', $exam))->assertRedirect(route('login'));
        $this->delete(route('final-exams.delete', $exam))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access index.
     */
    public function test_authenticated_user_can_access_final_exams_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        FinalExams::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson(route('final-exams.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /**
     * Test authenticated user can store final exam.
     */
    public function test_authenticated_user_can_store_final_exam(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $class = Classes::factory()->create();

        $examData = [
            'class_id' => $class->id,
            'title' => 'UAS Matematika',
            'exam_date' => '2026-07-18',
            'description' => 'Ujian Akhir Semester Ganjil.',
        ];

        $response = $this->actingAs($user)->postJson(route('final-exams.store'), $examData);

        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Final exam created successfully.');
        $response->assertJsonPath('data.title', 'UAS Matematika');

        $this->assertDatabaseHas('final_exams', [
            'class_id' => $class->id,
            'title' => 'UAS Matematika',
            'description' => 'Ujian Akhir Semester Ganjil.',
        ]);

        $storedExam = FinalExams::first();
        $this->assertEquals('2026-07-18', $storedExam->exam_date->format('Y-m-d'));
    }

    /**
     * Test validation rules on store.
     */
    public function test_store_final_exam_validation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // Test required fields
        $response = $this->actingAs($user)->post(route('final-exams.store'), []);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'class_id', 'title', 'exam_date',
        ]);

        // Test invalid class_id
        $response = $this->actingAs($user)->post(route('final-exams.store'), [
            'class_id' => 9999,
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['class_id']);
    }

    /**
     * Test authenticated user can view details.
     */
    public function test_authenticated_user_can_view_final_exam_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $exam = FinalExams::factory()->create();

        $response = $this->actingAs($user)->getJson(route('final-exams.show', $exam));

        $response->assertStatus(200);
        $response->assertJsonPath('id', $exam->id);
        $response->assertJsonPath('title', $exam->title);
    }

    /**
     * Test authenticated user can update final exam.
     */
    public function test_authenticated_user_can_update_final_exam(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $exam = FinalExams::factory()->create([
            'title' => 'UAS Lama',
            'description' => 'Deskripsi lama.',
        ]);
        $newClass = Classes::factory()->create();

        $updateData = [
            'class_id' => $newClass->id,
            'title' => 'UAS Baru',
            'exam_date' => '2026-07-19',
            'description' => 'Deskripsi baru.',
        ];

        $response = $this->actingAs($user)->putJson(route('final-exams.update', $exam), $updateData);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Final exam updated successfully.');
        $response->assertJsonPath('data.title', 'UAS Baru');
        $response->assertJsonPath('data.description', 'Deskripsi baru.');

        $this->assertDatabaseHas('final_exams', [
            'id' => $exam->id,
            'class_id' => $newClass->id,
            'title' => 'UAS Baru',
            'description' => 'Deskripsi baru.',
        ]);

        $this->assertEquals('2026-07-19', $exam->refresh()->exam_date->format('Y-m-d'));
    }

    /**
     * Test authenticated user can destroy final exam.
     */
    public function test_authenticated_user_can_delete_final_exam_via_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $exam = FinalExams::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('final-exams.destroy', $exam));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Final exam deleted successfully.');

        $this->assertDatabaseMissing('final_exams', [
            'id' => $exam->id,
        ]);
    }

    /**
     * Test authenticated user can delete via delete route.
     */
    public function test_authenticated_user_can_delete_final_exam_via_delete_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $exam = FinalExams::factory()->create();

        $response = $this->actingAs($user)->deleteJson(route('final-exams.delete', $exam));

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Final exam deleted successfully.');

        $this->assertDatabaseMissing('final_exams', [
            'id' => $exam->id,
        ]);
    }
}
