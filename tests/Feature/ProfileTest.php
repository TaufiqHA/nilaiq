<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest cannot access profile page.
     */
    public function test_guest_cannot_access_profile_page(): void
    {
        $response = $this->get(route('profile'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can view profile page.
     */
    public function test_authenticated_user_can_view_profile_page(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee($user->email);
    }

    /**
     * Test user can update profile info.
     */
    public function test_user_can_update_profile_info(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);
    }

    /**
     * Test user cannot update profile with existing email of another user.
     */
    public function test_user_cannot_update_profile_with_existing_email(): void
    {
        /** @var User $otherUser */
        $otherUser = User::factory()->create([
            'email' => 'other@example.com',
        ]);

        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $response = $this->actingAs($user)->from(route('profile'))->put(route('profile.update'), [
            'name' => 'User Name',
            'email' => 'other@example.com',
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test user can change password.
     */
    public function test_user_can_change_password(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    /**
     * Test user cannot change password with incorrect current password.
     */
    public function test_user_cannot_change_password_with_incorrect_current_password(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->actingAs($user)->from(route('profile'))->put(route('profile.password'), [
            'current_password' => 'incorrect-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHasErrors('current_password');
    }
}
