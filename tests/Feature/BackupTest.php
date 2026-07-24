<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_backup_routes(): void
    {
        $this->get(route('backup.index'))->assertRedirect(route('login'));
        $this->post(route('backup.store'))->assertRedirect(route('login'));
        $this->get(route('backup.download', 'test.zip'))->assertRedirect(route('login'));
        $this->delete(route('backup.destroy', 'test.zip'))->assertRedirect(route('login'));
    }

    /**
     * Test wali_kelas role is unauthorized to access backup routes.
     */
    public function test_wali_kelas_role_cannot_access_backup_routes(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role' => 'wali_kelas',
        ]);

        $this->actingAs($user)->get(route('backup.index'))->assertRedirect(route('wali-kelas.dashboard'));
        $this->actingAs($user)->post(route('backup.store'))->assertRedirect(route('wali-kelas.dashboard'));
        $this->actingAs($user)->get(route('backup.download', 'test.zip'))->assertRedirect(route('wali-kelas.dashboard'));
        $this->actingAs($user)->delete(route('backup.destroy', 'test.zip'))->assertRedirect(route('wali-kelas.dashboard'));
    }

    /**
     * Test mapel role can access backup index.
     */
    public function test_mapel_role_can_access_backup_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role' => 'mapel',
        ]);

        Storage::fake('backups');
        $folder = config('backup.backup.name', 'Laravel');
        Storage::disk('backups')->put($folder.'/backup-test.zip', 'dummy content');

        $response = $this->actingAs($user)->get(route('backup.index'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.backup');
        $response->assertViewHas('backups');
        $response->assertSee('backup-test.zip');
    }

    /**
     * Test mapel role can trigger backup store.
     */
    public function test_mapel_role_can_trigger_backup_store(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role' => 'mapel',
        ]);

        Artisan::shouldReceive('call')
            ->once()
            ->with('backup:run', ['--only-db' => true])
            ->andReturn(0);

        $response = $this->actingAs($user)->post(route('backup.store'));

        $response->assertRedirect(route('backup.index'));
        $response->assertSessionHas('success');
    }

    /**
     * Test mapel role can download backup file.
     */
    public function test_mapel_role_can_download_backup(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role' => 'mapel',
        ]);

        Storage::fake('backups');
        $folder = config('backup.backup.name', 'Laravel');
        Storage::disk('backups')->put($folder.'/backup-test.zip', 'dummy content');

        $response = $this->actingAs($user)->get(route('backup.download', 'backup-test.zip'));

        $response->assertStatus(200);
        $this->assertTrue($response->headers->get('Content-Disposition') !== null);
        $this->assertStringContainsString('attachment; filename=backup-test.zip', $response->headers->get('Content-Disposition'));
    }

    /**
     * Test mapel role can delete backup file.
     */
    public function test_mapel_role_can_delete_backup(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'role' => 'mapel',
        ]);

        Storage::fake('backups');
        $folder = config('backup.backup.name', 'Laravel');
        Storage::disk('backups')->put($folder.'/backup-test.zip', 'dummy content');

        $this->assertTrue(Storage::disk('backups')->exists($folder.'/backup-test.zip'));

        $response = $this->actingAs($user)->delete(route('backup.destroy', 'backup-test.zip'));

        $response->assertRedirect(route('backup.index'));
        $response->assertSessionHas('success', 'File backup berhasil dihapus.');
        $this->assertFalse(Storage::disk('backups')->exists($folder.'/backup-test.zip'));
    }
}
