<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest cannot access admin users index page.
     */
    public function test_guest_cannot_access_users_page(): void
    {
        $response = $this->get('/admin/users');

        $response->assertRedirect(route('login'));
    }

    /**
     * Test mapel user cannot access admin users page.
     */
    public function test_mapel_user_cannot_access_users_page(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);

        $response = $this->actingAs($user)->get('/admin/users');

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'Akses ditolak. Halaman ini khusus untuk Admin.');
    }

    /**
     * Test wali kelas user cannot access admin users page.
     */
    public function test_wali_kelas_user_cannot_access_users_page(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'wali_kelas']);

        $response = $this->actingAs($user)->get('/admin/users');

        $response->assertRedirect(route('wali-kelas.dashboard'));
        $response->assertSessionHas('error', 'Akses ditolak. Halaman ini khusus untuk Admin.');
    }

    /**
     * Test admin user can access admin users page.
     */
    public function test_admin_user_can_access_users_page(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Manajemen User');
    }

    /**
     * Test admin can view list of users.
     */
    public function test_admin_can_view_users_list(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        /** @var User $otherUser */
        $otherUser = User::factory()->create([
            'name' => 'Budi Setiawan',
            'email' => 'budi@nilaiq.com',
            'role' => 'mapel',
        ]);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Budi Setiawan');
        $response->assertSee('budi@nilaiq.com');
    }

    /**
     * Test admin can create a new user.
     */
    public function test_admin_can_create_user(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Guru Baru',
            'email' => 'gurubaru@nilaiq.com',
            'password' => 'password123',
            'role' => 'mapel',
        ]);

        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success', 'User berhasil ditambahkan.');

        $this->assertDatabaseHas('users', [
            'name' => 'Guru Baru',
            'email' => 'gurubaru@nilaiq.com',
            'role' => 'mapel',
        ]);

        $newUser = User::where('email', 'gurubaru@nilaiq.com')->first();
        $this->assertTrue(Hash::check('password123', $newUser->password));
    }

    /**
     * Test admin can update an existing user without password.
     */
    public function test_admin_can_update_user_without_password(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@nilaiq.com',
            'role' => 'mapel',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->actingAs($admin)->put("/admin/users/{$user->id}", [
            'name' => 'New Name',
            'email' => 'new@nilaiq.com',
            'role' => 'wali_kelas',
            'password' => '', // blank to keep old password
        ]);

        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success', 'User berhasil diperbarui.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@nilaiq.com',
            'role' => 'wali_kelas',
        ]);

        // Assert password has not changed
        $this->assertTrue(Hash::check('secret123', $user->fresh()->password));
    }

    /**
     * Test admin can update an existing user with new password.
     */
    public function test_admin_can_update_user_with_password(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        /** @var User $user */
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@nilaiq.com',
            'role' => 'mapel',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->actingAs($admin)->put("/admin/users/{$user->id}", [
            'name' => 'New Name',
            'email' => 'new@nilaiq.com',
            'role' => 'wali_kelas',
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success', 'User berhasil diperbarui.');

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /**
     * Test admin can delete other users.
     */
    public function test_admin_can_delete_other_user(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);

        $response = $this->actingAs($admin)->delete("/admin/users/{$user->id}");

        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('success', 'User berhasil dihapus.');

        $this->assertModelMissing($user);
    }

    /**
     * Test admin cannot delete themselves.
     */
    public function test_admin_cannot_delete_self(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->delete("/admin/users/{$admin->id}");

        $response->assertRedirect('/admin/users');
        $response->assertSessionHas('error', 'Anda tidak dapat menghapus akun Anda sendiri.');

        $this->assertModelExists($admin);
    }

    /**
     * Test user search and filter.
     */
    public function test_admin_can_filter_and_search_users(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        /** @var User $user1 */
        $user1 = User::factory()->create([
            'name' => 'Asep Surasep',
            'email' => 'asep@nilaiq.com',
            'role' => 'mapel',
        ]);

        /** @var User $user2 */
        $user2 = User::factory()->create([
            'name' => 'Siti Aminah',
            'email' => 'siti@nilaiq.com',
            'role' => 'wali_kelas',
        ]);

        // Search by name
        $response = $this->actingAs($admin)->get('/admin/users?search=Asep');
        $response->assertSee('Asep Surasep');
        $response->assertDontSee('Siti Aminah');

        // Filter by role
        $response = $this->actingAs($admin)->get('/admin/users?role=wali_kelas');
        $response->assertSee('Siti Aminah');
        $response->assertDontSee('Asep Surasep');
    }
}
