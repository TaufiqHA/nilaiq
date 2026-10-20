<?php

namespace Tests\Feature\Billing;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.midtrans.server_key' => 'SB-Mid-server-test']);
    }

    /**
     * Test a valid settlement notification activates the subscription.
     */
    public function test_valid_settlement_activates_subscription(): void
    {
        $plan = Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        $trial = Subscription::factory()->trial()->create(['user_id' => $user->id]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gross_amount' => $plan->price,
        ]);

        $this->postJson('/midtrans/notification', $this->payload($order))
            ->assertOk()
            ->assertJson(['ok' => true]);

        $order->refresh();
        $this->assertSame(Order::STATUS_SETTLEMENT, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertNotNull($order->subscription_id);

        $subscription = $user->fresh()->currentSubscription();

        $this->assertNotNull($subscription);
        $this->assertSame(Subscription::STATUS_ACTIVE, $subscription->status);
        $this->assertEqualsWithDelta(
            $trial->ends_at->copy()->addDays(365)->timestamp,
            $subscription->ends_at->timestamp,
            1,
        );
        $this->assertTrue($user->fresh()->hasAppAccess());
    }

    /**
     * Test duplicated notifications never extend the subscription twice.
     */
    public function test_duplicate_notification_is_idempotent(): void
    {
        $plan = Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        Subscription::factory()->trial()->create(['user_id' => $user->id]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gross_amount' => $plan->price,
        ]);

        $this->postJson('/midtrans/notification', $this->payload($order))->assertOk();
        $endsAt = $user->fresh()->currentSubscription()->ends_at->copy();

        $this->postJson('/midtrans/notification', $this->payload($order))->assertOk();

        $this->assertSame(1, Subscription::query()->where('user_id', $user->id)->count());
        $this->assertTrue($user->fresh()->currentSubscription()->ends_at->equalTo($endsAt));
    }

    /**
     * Test notifications with an invalid signature are rejected.
     */
    public function test_invalid_signature_is_rejected(): void
    {
        $plan = Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gross_amount' => $plan->price,
        ]);

        $this->postJson('/midtrans/notification', $this->payload($order, signature: 'invalid-signature'))
            ->assertStatus(403);

        $this->assertSame(Order::STATUS_PENDING, $order->fresh()->status);
        $this->assertFalse($user->fresh()->hasAppAccess());
    }

    /**
     * Test a notification with the wrong amount is rejected.
     */
    public function test_amount_mismatch_is_rejected(): void
    {
        $plan = Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gross_amount' => $plan->price,
        ]);

        $this->postJson('/midtrans/notification', $this->payload($order, grossAmount: '999.00'))
            ->assertStatus(400);

        $this->assertSame(Order::STATUS_PENDING, $order->fresh()->status);
    }

    /**
     * Test an expired transaction never grants access.
     */
    public function test_expired_transaction_does_not_grant_access(): void
    {
        $plan = Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gross_amount' => $plan->price,
        ]);

        $this->postJson('/midtrans/notification', $this->payload($order, status: 'expire'))
            ->assertOk();

        $this->assertSame(Order::STATUS_EXPIRE, $order->fresh()->status);
        $this->assertFalse($user->fresh()->hasAppAccess());
    }

    /**
     * Test a renewal adds the new period after the current end date.
     */
    public function test_renewal_extends_from_the_existing_end_date(): void
    {
        $plan = Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        $current = Subscription::factory()->create([
            'user_id' => $user->id,
            'ends_at' => now()->addDays(30),
        ]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'gross_amount' => $plan->price,
        ]);

        $this->postJson('/midtrans/notification', $this->payload($order))->assertOk();

        $endsAt = $user->fresh()->currentSubscription()->ends_at;

        $this->assertEqualsWithDelta(
            $current->ends_at->copy()->addDays(365)->timestamp,
            $endsAt->timestamp,
            1,
        );
    }

    /**
     * Build a signed Midtrans notification payload.
     *
     * @return array<string, mixed>
     */
    private function payload(
        Order $order,
        string $status = 'settlement',
        string $grossAmount = '150000.00',
        ?string $signature = null,
    ): array {
        $payload = [
            'order_id' => $order->order_id,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'transaction_status' => $status,
            'transaction_id' => 'trx-'.$order->id,
            'payment_type' => 'bank_transfer',
            'fraud_status' => 'accept',
        ];

        $payload['signature_key'] = $signature ?? hash(
            'sha512',
            $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('services.midtrans.server_key'),
        );

        return $payload;
    }
}
