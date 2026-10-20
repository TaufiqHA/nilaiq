<?php

namespace Tests\Feature\Billing;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutTest extends TestCase
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
     * Test the billing page shows trial information.
     */
    public function test_trial_user_can_view_billing_page(): void
    {
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        Subscription::factory()->trial()->create(['user_id' => $user->id]);
        Plan::factory()->create();

        $this->actingAs($user)
            ->get(route('billing.index'))
            ->assertOk()
            ->assertSee('Trial')
            ->assertSee('Bayar & Aktifkan Langganan')
            ->assertSee('Riwayat Transaksi');
    }

    /**
     * Test the renewal button is hidden before the five day window.
     */
    public function test_billing_page_hides_renewal_before_the_window(): void
    {
        Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => Subscription::STATUS_ACTIVE,
            'ends_at' => now()->addDays(10),
        ]);

        $this->actingAs($user)
            ->get(route('billing.index'))
            ->assertOk()
            ->assertDontSee('Perpanjang 1 Tahun')
            ->assertDontSee('Perpanjangan belum dibuka')
            ->assertDontSee('Pembayaran diproses aman oleh Midtrans');
    }

    /**
     * Test the renewal button appears inside the five day window.
     */
    public function test_billing_page_shows_renewal_inside_the_window(): void
    {
        Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => Subscription::STATUS_ACTIVE,
            'ends_at' => now()->addDays(5),
        ]);

        $this->actingAs($user)
            ->get(route('billing.index'))
            ->assertOk()
            ->assertSee('Perpanjang 1 Tahun');
    }

    /**
     * Test early renewal is rejected by the backend.
     */
    public function test_checkout_rejects_early_renewal(): void
    {
        Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => Subscription::STATUS_ACTIVE,
            'ends_at' => now()->addDays(10),
        ]);

        $this->actingAs($user)
            ->postJson(route('billing.checkout'))
            ->assertStatus(422)
            ->assertJsonPath('message', 'Perpanjangan dapat dilakukan mulai 5 hari sebelum masa aktif berakhir.');

        $this->assertSame(0, Order::query()->count());
    }

    /**
     * Test renewal is allowed inside the five day window.
     */
    public function test_checkout_allows_renewal_inside_the_window(): void
    {
        Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => Subscription::STATUS_ACTIVE,
            'ends_at' => now()->addDays(5),
        ]);

        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'renewal-token',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/renewal-token',
            ], 201),
        ]);

        $this->actingAs($user)
            ->postJson(route('billing.checkout'))
            ->assertOk()
            ->assertJsonPath('token', 'renewal-token');

        $this->assertSame(1, Order::query()->count());
    }

    /**
     * Test lifetime accounts cannot start a checkout.
     */
    public function test_lifetime_account_cannot_checkout(): void
    {
        Plan::factory()->create();
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('billing.checkout'))
            ->assertStatus(422)
            ->assertJsonPath('message', 'Akun Anda memiliki akses selamanya. Tidak ada pembayaran yang diperlukan.');

        $this->assertSame(0, Order::query()->count());
    }

    /**
     * Test checkout creates a pending order and returns the Snap redirect.
     */
    public function test_checkout_creates_order_and_redirects_to_midtrans(): void
    {
        $plan = Plan::factory()->create();

        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-token-123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-123',
            ], 201),
        ]);

        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();

        $this->actingAs($user)
            ->post(route('billing.checkout'))
            ->assertRedirect('https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-123');

        $order = Order::query()->firstOrFail();

        $this->assertSame($user->id, $order->user_id);
        $this->assertSame($plan->id, $order->plan_id);
        $this->assertSame($plan->price, $order->gross_amount);
        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame('snap-token-123', $order->snap_token);
        $this->assertNotNull($order->expires_at);

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://app.sandbox.midtrans.com/snap/v1/transactions'
                && $request['transaction_details']['gross_amount'] === 150000
                && $request['item_details'][0]['price'] === 150000
                && $request->hasHeader('Authorization');
        });
    }

    /**
     * Test an existing pending order is reused instead of duplicated.
     */
    public function test_checkout_reuses_pending_order(): void
    {
        Plan::factory()->create();

        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-token-123',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-123',
            ], 201),
        ]);

        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();

        $this->actingAs($user)->post(route('billing.checkout'));
        $this->actingAs($user)->post(route('billing.checkout'));

        $this->assertSame(1, Order::query()->count());
        Http::assertSentCount(1);
    }

    /**
     * Test checkout returns JSON with the Snap token when requested.
     */
    public function test_checkout_returns_json_for_ajax_requests(): void
    {
        Plan::factory()->create();

        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'token' => 'snap-token-ajax',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-ajax',
            ], 201),
        ]);

        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();

        $this->actingAs($user)
            ->postJson(route('billing.checkout'))
            ->assertOk()
            ->assertJson([
                'token' => 'snap-token-ajax',
                'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/snap-token-ajax',
            ]);
    }

    /**
     * Test a Midtrans failure is reported and the order is marked failed.
     */
    public function test_checkout_handles_midtrans_failure(): void
    {
        Plan::factory()->create();

        Http::fake([
            'app.sandbox.midtrans.com/snap/v1/transactions' => Http::response([
                'status_message' => 'Internal error',
            ], 500),
        ]);

        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();

        $this->actingAs($user)
            ->postJson(route('billing.checkout'))
            ->assertStatus(422)
            ->assertJsonPath('message', 'Gagal membuat transaksi pembayaran. Silakan coba lagi.');

        $this->assertSame(Order::STATUS_FAILURE, Order::query()->firstOrFail()->status);
    }

    /**
     * Test checkout refuses to run without Midtrans credentials.
     */
    public function test_checkout_requires_midtrans_configuration(): void
    {
        Plan::factory()->create();
        config(['services.midtrans.server_key' => '']);

        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();

        $this->actingAs($user)
            ->postJson(route('billing.checkout'))
            ->assertStatus(422)
            ->assertJsonPath('message', 'Pembayaran belum dikonfigurasi. Silakan hubungi admin.');

        $this->assertSame(0, Order::query()->count());
    }
}
