<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMonitoringTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest cannot access admin monitoring page.
     */
    public function test_guest_cannot_access_monitoring_page(): void
    {
        $response = $this->get('/admin/monitoring');

        $response->assertRedirect(route('login'));
    }

    /**
     * Test mapel user cannot access admin monitoring page.
     */
    public function test_mapel_user_cannot_access_monitoring_page(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'mapel']);

        $response = $this->actingAs($user)->get('/admin/monitoring');

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'Akses ditolak. Halaman ini khusus untuk Admin.');
    }

    /**
     * Test wali kelas user cannot access admin monitoring page.
     */
    public function test_wali_kelas_user_cannot_access_monitoring_page(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'wali_kelas']);

        $response = $this->actingAs($user)->get('/admin/monitoring');

        $response->assertRedirect(route('wali-kelas.dashboard'));
        $response->assertSessionHas('error', 'Akses ditolak. Halaman ini khusus untuk Admin.');
    }

    /**
     * Test admin user can access admin monitoring page.
     */
    public function test_admin_user_can_access_monitoring_page(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/admin/monitoring');

        $response->assertStatus(200);
        $response->assertSee('VPS Resource Monitoring');
    }

    /**
     * Test admin user can fetch monitoring stats as JSON.
     */
    public function test_admin_user_can_fetch_monitoring_json_updates(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->getJson('/admin/monitoring');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'cpu',
            'memory' => ['total', 'used', 'free', 'percentage'],
            'disk' => ['total', 'used', 'free', 'percentage'],
            'uptime',
            'db_status',
            'db_error',
            'system' => ['os', 'php_version', 'laravel_version', 'server_software', 'database_driver', 'cache_driver'],
        ]);
    }

    /**
     * Test admin login redirects to monitoring.
     */
    public function test_admin_login_redirects_to_monitoring(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => bcrypt('admin-password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'admin-password',
        ]);

        $response->assertRedirect(route('admin.monitoring'));
        $this->assertAuthenticatedAs($user);
    }
}
