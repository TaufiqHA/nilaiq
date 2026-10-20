<?php

namespace Tests\Feature\Billing;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Midtrans\MidtransClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.midtrans.server_key' => 'SB-Mid-server-test',
            'services.midtrans.client_key' => 'SB-Mid-client-test',
            'services.midtrans.is_production' => false,
        ]);
    }

    /**
     * Test the sync action uses the Midtrans API host and activates access.
     */
    public function test_manual_sync_uses_the_api_host_and_activates_subscription(): void
    {
        $plan = Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'snap_token' => 'snap-token',
            'gross_amount' => $plan->price,
        ]);

        Http::fake([
            'api.sandbox.midtrans.com/v2/*/status' => Http::response($this->settlementPayload($order)),
        ]);

        $this->actingAs($user)
            ->post(route('billing.sync'))
            ->assertRedirect(route('billing.index'));

        $order->refresh();

        $this->assertSame(Order::STATUS_SETTLEMENT, $order->status);
        $this->assertSame(Subscription::STATUS_ACTIVE, $user->fresh()->currentSubscription()?->status);

        Http::assertSent(function (Request $request) use ($order): bool {
            return $request->url() === 'https://api.sandbox.midtrans.com/v2/'.$order->order_id.'/status'
                && $request->hasHeader('Authorization');
        });
    }

    /**
     * Test the reconciliation command settles pending orders.
     */
    public function test_command_settles_pending_orders(): void
    {
        $plan = Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'snap_token' => 'snap-token',
            'gross_amount' => $plan->price,
        ]);

        Http::fake([
            'api.sandbox.midtrans.com/v2/*/status' => Http::response($this->settlementPayload($order)),
        ]);

        $this->artisan('orders:sync-pending')->assertSuccessful();

        $this->assertSame(Order::STATUS_SETTLEMENT, $order->fresh()->status);
        $this->assertSame(Subscription::STATUS_ACTIVE, $user->fresh()->currentSubscription()?->status);
    }

    /**
     * Test a mismatched amount is never treated as paid.
     */
    public function test_command_rejects_amount_mismatch(): void
    {
        $plan = Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'snap_token' => 'snap-token',
            'gross_amount' => $plan->price,
        ]);

        Http::fake([
            'api.sandbox.midtrans.com/v2/*/status' => Http::response([
                ...$this->settlementPayload($order),
                'gross_amount' => '999.00',
            ]),
        ]);

        $this->artisan('orders:sync-pending')->assertSuccessful();

        $this->assertSame(Order::STATUS_PENDING, $order->fresh()->status);
        $this->assertNull($user->fresh()->currentSubscription());
    }

    /**
     * Test production status requests use the production API host.
     */
    public function test_production_status_uses_the_production_api_host(): void
    {
        config(['services.midtrans.is_production' => true]);

        Http::fake([
            'api.midtrans.com/v2/ORDER-123/status' => Http::response([
                'order_id' => 'ORDER-123',
                'transaction_status' => 'pending',
                'gross_amount' => '150000.00',
            ]),
        ]);

        app(MidtransClient::class)->getTransactionStatus('ORDER-123');

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.midtrans.com/v2/ORDER-123/status');
    }

    /**
     * Build a successful Midtrans status payload.
     *
     * @return array<string, mixed>
     */
    private function settlementPayload(Order $order): array
    {
        return [
            'order_id' => $order->order_id,
            'transaction_status' => 'settlement',
            'status_code' => '200',
            'gross_amount' => number_format($order->gross_amount, 2, '.', ''),
            'transaction_id' => 'trx-'.$order->id,
            'payment_type' => 'bank_transfer',
            'fraud_status' => 'accept',
        ];
    }
}
