<?php

namespace Tests\Feature;

use App\Models\Settings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private static ?string $tempViewPath = null;

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$tempViewPath === null) {
            self::$tempViewPath = sys_get_temp_dir().'/nilaiq_views_'.uniqid();
            mkdir(self::$tempViewPath.'/auth/settings', 0777, true);
            file_put_contents(self::$tempViewPath.'/auth/settings/index.blade.php', 'dummy');
            file_put_contents(self::$tempViewPath.'/auth/settings/show.blade.php', 'dummy');
        }

        View::addLocation(self::$tempViewPath);
    }

    /**
     * Test guests are redirected to login.
     */
    public function test_guest_cannot_access_settings(): void
    {
        $setting = Settings::factory()->create();

        $this->get(route('settings.index'))->assertRedirect(route('login'));
        $this->get(route('settings.show', $setting))->assertRedirect(route('login'));
        $this->post(route('settings.store'), [])->assertRedirect(route('login'));
        $this->put(route('settings.update', $setting), [])->assertRedirect(route('login'));
        $this->delete(route('settings.destroy', $setting))->assertRedirect(route('login'));
        $this->delete(route('settings.delete', $setting))->assertRedirect(route('login'));
    }

    /**
     * Test authenticated user can access settings index.
     */
    public function test_authenticated_user_can_access_settings_index(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        Settings::factory()->count(3)->create();

        $response = $this->actingAs($user)->get(route('settings.index'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.MasterData');
        $response->assertViewHas('setting');
    }

    /**
     * Test authenticated user can access settings index JSON.
     */
    public function test_authenticated_user_can_access_settings_index_json(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $setting = Settings::factory()->create();

        $response = $this->actingAs($user)->getJson(route('settings.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'school_name' => $setting->school_name,
        ]);
    }

    /**
     * Test authenticated user can store setting.
     */
    public function test_authenticated_user_can_store_setting(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $settingData = Settings::factory()->make()->toArray();

        $response = $this->actingAs($user)->post(route('settings.store'), $settingData);

        $this->assertDatabaseHas('settings', [
            'school_name' => $settingData['school_name'],
            'npsn' => $settingData['npsn'],
        ]);

        $setting = Settings::first();
        $response->assertRedirect(route('master-data.index'));
        $response->assertSessionHas('success', 'Settings saved successfully.');
    }

    /**
     * Test authenticated user can store setting via JSON.
     */
    public function test_authenticated_user_can_store_setting_json(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $settingData = Settings::factory()->make()->toArray();

        $response = $this->actingAs($user)->postJson(route('settings.store'), $settingData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'school_name',
                'npsn',
            ],
        ]);
    }

    /**
     * Test authenticated user can view specific setting.
     */
    public function test_authenticated_user_can_view_specific_setting(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $setting = Settings::factory()->create();

        $response = $this->actingAs($user)->get(route('settings.show', $setting));

        $response->assertStatus(200);
        $response->assertViewIs('auth.MasterData');
        $response->assertViewHas('setting');
    }

    /**
     * Test authenticated user can view specific setting via JSON.
     */
    public function test_authenticated_user_can_view_specific_setting_json(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $setting = Settings::factory()->create();

        $response = $this->actingAs($user)->getJson(route('settings.show', $setting));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $setting->id,
            'school_name' => $setting->school_name,
        ]);
    }

    /**
     * Test authenticated user can update setting.
     */
    public function test_authenticated_user_can_update_setting(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $setting = Settings::factory()->create();
        $updatedData = Settings::factory()->make()->toArray();

        $response = $this->actingAs($user)->put(route('settings.update', $setting), $updatedData);

        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
            'school_name' => $updatedData['school_name'],
        ]);

        $response->assertRedirect(route('master-data.index'));
        $response->assertSessionHas('success', 'Settings updated successfully.');
    }

    /**
     * Test authenticated user can update setting via JSON.
     */
    public function test_authenticated_user_can_update_setting_json(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $setting = Settings::factory()->create();
        $updatedData = Settings::factory()->make()->toArray();

        $response = $this->actingAs($user)->putJson(route('settings.update', $setting), $updatedData);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data',
        ]);
    }

    /**
     * Test authenticated user can destroy setting.
     */
    public function test_authenticated_user_can_destroy_setting(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $setting = Settings::factory()->create();

        $response = $this->actingAs($user)->delete(route('settings.destroy', $setting));

        $this->assertDatabaseMissing('settings', [
            'id' => $setting->id,
        ]);

        $response->assertRedirect(route('settings.index'));
        $response->assertSessionHas('success', 'Settings deleted successfully.');
    }

    /**
     * Test authenticated user can delete setting via alias.
     */
    public function test_authenticated_user_can_delete_setting_via_alias(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $setting = Settings::factory()->create();

        $response = $this->actingAs($user)->delete(route('settings.delete', $setting));

        $this->assertDatabaseMissing('settings', [
            'id' => $setting->id,
        ]);

        $response->assertRedirect(route('settings.index'));
    }

    /**
     * Test setting validation.
     */
    public function test_setting_validation_errors(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // 1. Invalid email format
        $response = $this->actingAs($user)->post(route('settings.store'), [
            'teacher_email' => 'not-an-email',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['teacher_email']);

        // 2. Non-numeric score/weights
        $response = $this->actingAs($user)->post(route('settings.store'), [
            'minimum_score' => 'invalid-score',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['minimum_score']);
    }

    /**
     * Clean up the temporary view directory after class tests have completed.
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        if (self::$tempViewPath !== null && is_dir(self::$tempViewPath)) {
            self::deleteDir(self::$tempViewPath);
        }
    }

    /**
     * Recursively delete a directory.
     */
    private static function deleteDir(string $dirPath): void
    {
        if (! is_dir($dirPath)) {
            return;
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath.'*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
}
