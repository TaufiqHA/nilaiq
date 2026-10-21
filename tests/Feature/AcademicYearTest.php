<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_academic_years(): void
    {
        $year = AcademicYear::factory()->create();

        $this->get(route('academic-years.index'))->assertRedirect(route('login'));
        $this->get(route('academic-years.show', $year))->assertRedirect(route('login'));
        $this->post(route('academic-years.store'), [])->assertRedirect(route('login'));
        $this->put(route('academic-years.update', $year), [])->assertRedirect(route('login'));
        $this->post(route('academic-years.activate', $year))->assertRedirect(route('login'));
        $this->delete(route('academic-years.destroy', $year))->assertRedirect(route('login'));
        $this->delete(route('academic-years.delete', $year))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access index.
     */
    public function test_authenticated_user_can_access_academic_years_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        AcademicYear::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('academic-years.index'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.AcademicYear');
        $response->assertViewHas('academicYears');
    }

    /**
     * Test authenticated user can access index JSON.
     */
    public function test_authenticated_user_can_access_academic_years_index_json(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $year = AcademicYear::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson(route('academic-years.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'year' => $year->year,
            'semester' => $year->semester,
        ]);
    }

    /**
     * Test authenticated user can store.
     */
    public function test_authenticated_user_can_store_academic_year(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $yearData = [
            'year' => '2025/2026',
            'semester' => 'GANJIL',
            'is_active' => '1',
        ];

        $response = $this->actingAs($user)->post(route('academic-years.store'), $yearData);

        $this->assertDatabaseHas('academic_years', [
            'year' => '2025/2026',
            'semester' => 'GANJIL',
            'is_active' => true,
            'user_id' => $user->id,
        ]);

        $response->assertRedirect(route('academic-years.index'));
        $response->assertSessionHas('success', 'Academic year created successfully.');
    }

    /**
     * Test active academic year uniqueness logic on store.
     */
    public function test_only_one_academic_year_can_be_active_on_store(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $activeYear = AcademicYear::factory()->active()->create(['user_id' => $user->id]);

        $yearData = [
            'year' => '2026/2027',
            'semester' => 'GENAP',
            'is_active' => '1',
        ];

        $this->actingAs($user)->post(route('academic-years.store'), $yearData);

        $this->assertDatabaseHas('academic_years', [
            'year' => '2026/2027',
            'is_active' => true,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('academic_years', [
            'id' => $activeYear->id,
            'is_active' => false,
        ]);
    }

    /**
     * Test authenticated user can view details.
     */
    public function test_authenticated_user_can_view_academic_year_details(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $year = AcademicYear::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('academic-years.show', $year));

        $response->assertStatus(200);
        $response->assertViewIs('auth.AcademicYear');
        $response->assertViewHas('academicYear');
    }

    /**
     * Test authenticated user can update.
     */
    public function test_authenticated_user_can_update_academic_year(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $year = AcademicYear::factory()->create(['year' => '2024/2025', 'user_id' => $user->id]);

        $updateData = [
            'year' => '2025/2026',
            'semester' => 'GENAP',
            'is_active' => '1',
        ];

        $response = $this->actingAs($user)->put(route('academic-years.update', $year), $updateData);

        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'year' => '2025/2026',
            'semester' => 'GENAP',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('academic-years.index'));
    }

    /**
     * Test active academic year uniqueness logic on update.
     */
    public function test_only_one_academic_year_can_be_active_on_update(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $activeYear = AcademicYear::factory()->active()->create(['user_id' => $user->id]);
        $anotherYear = AcademicYear::factory()->create(['is_active' => false, 'user_id' => $user->id]);

        $updateData = [
            'year' => $anotherYear->year,
            'semester' => $anotherYear->semester,
            'is_active' => '1',
        ];

        $this->actingAs($user)->put(route('academic-years.update', $anotherYear), $updateData);

        $this->assertDatabaseHas('academic_years', [
            'id' => $anotherYear->id,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('academic_years', [
            'id' => $activeYear->id,
            'is_active' => false,
        ]);
    }

    /**
     * Test activating a year deactivates the previously active year.
     */
    public function test_authenticated_user_can_activate_academic_year(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var User $otherUser */
        $otherUser = User::factory()->create();

        $activeYear = AcademicYear::factory()->active()->create(['user_id' => $user->id]);
        $otherUserActiveYear = AcademicYear::factory()->active()->create(['user_id' => $otherUser->id]);
        $targetYear = AcademicYear::factory()->create(['is_active' => false, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('academic-years.activate', $targetYear));

        $response->assertRedirect(route('academic-years.index'));
        $response->assertSessionHas('success', 'Academic year activated successfully.');

        $this->assertDatabaseHas('academic_years', [
            'id' => $targetYear->id,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('academic_years', [
            'id' => $activeYear->id,
            'is_active' => false,
        ]);

        $this->assertDatabaseHas('academic_years', [
            'id' => $otherUserActiveYear->id,
            'is_active' => true,
        ]);
    }

    /**
     * Test activating an already active year keeps it active.
     */
    public function test_activating_an_already_active_academic_year_is_idempotent(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $activeYear = AcademicYear::factory()->active()->create(['user_id' => $user->id]);

        $this->actingAs($user)->post(route('academic-years.activate', $activeYear))
            ->assertRedirect(route('academic-years.index'));

        $this->assertDatabaseHas('academic_years', [
            'id' => $activeYear->id,
            'is_active' => true,
        ]);
    }

    /**
     * Test activation returns JSON when requested.
     */
    public function test_activation_returns_json_when_requested(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $targetYear = AcademicYear::factory()->create(['is_active' => false, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson(route('academic-years.activate', $targetYear));

        $response->assertOk();
        $response->assertJsonPath('message', 'Academic year activated successfully.');
        $response->assertJsonPath('data.id', $targetYear->id);
        $response->assertJsonPath('data.is_active', true);
    }

    /**
     * Test user cannot activate another user's academic year.
     */
    public function test_user_cannot_activate_another_users_academic_year(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        /** @var User $user2 */
        $user2 = User::factory()->create();

        $yearOfUser2 = AcademicYear::factory()->create(['is_active' => false, 'user_id' => $user2->id]);

        $this->actingAs($user1)
            ->post(route('academic-years.activate', $yearOfUser2))
            ->assertNotFound();

        $this->assertDatabaseHas('academic_years', [
            'id' => $yearOfUser2->id,
            'is_active' => false,
        ]);
    }

    /**
     * Test the activate action is only rendered for inactive years.
     */
    public function test_activate_button_is_only_rendered_for_inactive_academic_years(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $activeYear = AcademicYear::factory()->active()->create(['user_id' => $user->id]);
        $inactiveYear = AcademicYear::factory()->create(['is_active' => false, 'user_id' => $user->id]);

        $content = $this->actingAs($user)->get(route('academic-years.index'))->getContent();

        $this->assertStringContainsString("openActivateModal({$inactiveYear->id},", $content);
        $this->assertStringNotContainsString("openActivateModal({$activeYear->id},", $content);
    }

    /**
     * Test authenticated user can destroy.
     */
    public function test_authenticated_user_can_delete_academic_year_via_destroy(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $year = AcademicYear::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('academic-years.destroy', $year));

        $this->assertDatabaseMissing('academic_years', [
            'id' => $year->id,
        ]);

        $response->assertRedirect(route('academic-years.index'));
    }

    /**
     * Test authenticated user can delete via delete route.
     */
    public function test_authenticated_user_can_delete_academic_year_via_delete_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $year = AcademicYear::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('academic-years.delete', $year));

        $this->assertDatabaseMissing('academic_years', [
            'id' => $year->id,
        ]);

        $response->assertRedirect(route('academic-years.index'));
    }

    /**
     * Test user cannot access or modify another user's academic year.
     */
    public function test_user_cannot_access_or_modify_other_users_academic_year(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        /** @var User $user2 */
        $user2 = User::factory()->create();

        $yearOfUser2 = AcademicYear::factory()->create(['user_id' => $user2->id]);

        // Attempting to view details
        $this->actingAs($user1)->get(route('academic-years.show', $yearOfUser2))
            ->assertNotFound();

        // Attempting to update
        $this->actingAs($user1)->put(route('academic-years.update', $yearOfUser2), [
            'year' => '2027/2028',
            'semester' => 'GENAP',
            'is_active' => '1',
        ])->assertNotFound();

        // Attempting to delete
        $this->actingAs($user1)->delete(route('academic-years.destroy', $yearOfUser2))
            ->assertNotFound();
    }
}
