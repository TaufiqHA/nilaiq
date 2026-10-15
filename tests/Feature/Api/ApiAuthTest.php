<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Health endpoint should be publicly reachable.
     */
    public function test_health_endpoint_is_public(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'ok');
    }

    /**
     * A guru mapel can login and receive a token.
     */
    public function test_mapel_user_can_login_and_receive_token(): void
    {
        $user = User::factory()->create([
            'email' => 'guru@example.com',
            'password' => 'password',
            'role' => 'mapel',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'guru@example.com',
            'password' => 'password',
            'device_name' => 'android',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);
        $response->assertJsonPath('user.id', $user->id);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'android',
        ]);
    }

    /**
     * Invalid credentials return a validation error.
     */
    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'guru@example.com',
            'password' => 'password',
            'role' => 'mapel',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'guru@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /**
     * Non mapel users are rejected.
     */
    public function test_non_mapel_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'wali@example.com',
            'password' => 'password',
            'role' => 'wali_kelas',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wali@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /**
     * Protected routes require a token.
     */
    public function test_protected_route_requires_authentication(): void
    {
        $this->getJson('/api/dashboard')->assertStatus(401);
        $this->getJson('/api/me')->assertStatus(401);
    }

    /**
     * Authenticated user can fetch their profile and logout.
     */
    public function test_authenticated_user_can_fetch_profile_and_logout(): void
    {
        $user = User::factory()->create(['role' => 'mapel']);
        $token = $user->createToken('mobile')->plainTextToken;

        $this->withToken($token)->getJson('/api/me')
            ->assertStatus(200)
            ->assertJsonPath('id', $user->id);

        $this->withToken($token)->postJson('/api/logout')->assertStatus(200);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
