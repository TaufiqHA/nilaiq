<?php

namespace Tests\Feature\Subscription;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest can view the registration page.
     */
    public function test_guest_can_view_register_page(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Buat akun guru');
    }

    /**
     * Test registration creates a three day trial and logs the user in.
     */
    public function test_registration_starts_a_three_day_trial(): void
    {
        $response = $this->post('/register', [
            'name' => 'Sari Wulandari',
            'email' => 'sari@example.com',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
            'role' => 'mapel',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'sari@example.com')->firstOrFail();
        $subscription = $user->currentSubscription();

        $this->assertNotNull($subscription);
        $this->assertSame(Subscription::STATUS_TRIALING, $subscription->status);
        $this->assertSame(Subscription::SOURCE_TRIAL, $subscription->source);
        $this->assertTrue($user->isOnTrial());
        $this->assertTrue($user->hasAppAccess());
        $this->assertTrue($subscription->ends_at->between(
            now()->addDays(3)->subMinute(),
            now()->addDays(3)->addMinute(),
        ));
    }

    /**
     * Test wali kelas registration redirects to the wali kelas dashboard.
     */
    public function test_wali_kelas_registration_redirects_to_wali_kelas_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
            'role' => 'wali_kelas',
        ]);

        $response->assertRedirect(route('wali-kelas.dashboard'));
        $this->assertTrue(User::query()->where('email', 'budi@example.com')->firstOrFail()->isOnTrial());
    }

    /**
     * Test registration rejects duplicate emails.
     */
    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'sari@example.com']);

        $this->post('/register', [
            'name' => 'Sari Lain',
            'email' => 'sari@example.com',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
            'role' => 'mapel',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    /**
     * Test an expired user is redirected to billing from app pages.
     */
    public function test_expired_user_is_redirected_to_billing(): void
    {
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        Subscription::factory()->expired()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('academic-years.index'))
            ->assertRedirect(route('billing.index'));

        $this->actingAs($user)
            ->get(route('billing.index'))
            ->assertOk()
            ->assertSee('Berakhir');
    }

    /**
     * Test admins bypass the subscription gate.
     */
    public function test_admin_without_subscription_can_access_admin_pages(): void
    {
        /** @var User $admin */
        $admin = User::factory()->withoutSubscription()->create(['role' => 'admin']);

        $this->assertTrue($admin->hasAppAccess());

        $this->actingAs($admin)
            ->get(route('admin.monitoring'))
            ->assertOk();
    }

    /**
     * Test legacy lifetime accounts keep access forever.
     */
    public function test_lifetime_subscription_grants_access(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $subscription = $user->currentSubscription();

        $this->assertNotNull($subscription);
        $this->assertTrue($subscription->isLifetime());
        $this->assertTrue($user->hasAppAccess());
        $this->assertFalse($user->isOnTrial());
    }
}
