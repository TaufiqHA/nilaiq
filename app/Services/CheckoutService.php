<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Midtrans\MidtransClient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class CheckoutService
{
    public function __construct(private MidtransClient $client) {}

    /**
     * Create or reuse a pending order and obtain a Snap token.
     */
    public function createOrder(User $user): Order
    {
        $plan = Plan::annual();

        if ($plan === null) {
            throw new RuntimeException('Paket langganan belum tersedia. Silakan hubungi admin.');
        }

        if (! $this->client->isConfigured()) {
            throw new RuntimeException('Pembayaran belum dikonfigurasi. Silakan hubungi admin.');
        }

        $currentSubscription = $user->currentSubscription();

        if ($currentSubscription?->isLifetime()) {
            throw new RuntimeException('Akun Anda memiliki akses selamanya. Tidak ada pembayaran yang diperlukan.');
        }

        $renewalWindow = (int) config('subscription.renewal_window_days', 5);

        if (
            $currentSubscription !== null
            && $currentSubscription->status === Subscription::STATUS_ACTIVE
            && $currentSubscription->daysLeft() > $renewalWindow
        ) {
            throw new RuntimeException(
                'Perpanjangan dapat dilakukan mulai '.$renewalWindow.' hari sebelum masa aktif berakhir.'
            );
        }

        $pendingOrder = $user->orders()
            ->pending()
            ->where('plan_id', $plan->id)
            ->whereNotNull('snap_token')
            ->where(function (Builder $query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->first();

        if ($pendingOrder !== null) {
            return $pendingOrder;
        }

        $order = $user->orders()->create([
            'plan_id' => $plan->id,
            'order_id' => $this->generateOrderId($user),
            'gross_amount' => $plan->price,
            'status' => Order::STATUS_PENDING,
            'expires_at' => now()->addDay(),
        ]);

        try {
            $snap = $this->client->createSnapTransaction($order, $user, $plan);
        } catch (Throwable $e) {
            $order->update(['status' => Order::STATUS_FAILURE]);

            Log::error('Gagal membuat transaksi Midtrans.', [
                'order_id' => $order->order_id,
                'exception' => $e,
            ]);

            throw new RuntimeException('Gagal membuat transaksi pembayaran. Silakan coba lagi.', 0, $e);
        }

        $order->update([
            'snap_token' => $snap['token'],
            'snap_redirect_url' => $snap['redirect_url'],
        ]);

        return $order->refresh();
    }

    /**
     * Build a unique Midtrans order id.
     */
    private function generateOrderId(User $user): string
    {
        return 'NQ-'.now()->format('YmdHis').'-'.$user->id.'-'.Str::upper(Str::random(6));
    }
}
