<?php

namespace Tests\Feature\Api;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.midtrans.server_key' => 'SB-Mid-server-test',
            'services.midtrans.client_key' => 'SB-Mid-client-test',
        ]);
    }

    /**
     * Test the mobile subscription endpoint returns the current entitlement.
     */
    public function test_subscription_endpoint_returns_entitlement(): void
    {
        Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/subscription')
            ->assertOk()
            ->assertJson([
                'has_access' => true,
                'is_trial' => false,
                'status' => Subscription::STATUS_ACTIVE,
            ])
            ->assertJsonPath('plan.price', 150000);
    }

    /**
     * Test expired users cannot access data routes but can still call /me.
     */
    public function test_expired_user_gets_payment_required_response(): void
    {
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        Subscription::factory()->expired()->create(['user_id' => $user->id]);
        Sanctum::actingAs($user);

        $this->getJson('/api/classes')
            ->assertStatus(402)
            ->assertJsonPath('code', 'subscription_required');

        $this->getJson('/api/me')->assertOk();
        $this->getJson('/api/subscription')->assertOk()->assertJsonPath('has_access', false);
    }

    /**
     * Test the mobile checkout endpoint returns a Snap token.
     */
    public function test_mobile_checkout_returns_snap_token(): void
    {
        Plan::factory()->create();

        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'mobile-snap-token',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/mobile-snap-token',
            ], 201),
        ]);

        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/subscription/checkout')
            ->assertOk()
            ->assertJsonPath('token', 'mobile-snap-token')
            ->assertJsonPath('gross_amount', 150000);

        $this->assertSame(Order::STATUS_PENDING, Order::query()->firstOrFail()->status);
    }
}
