<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\Midtrans\MidtransClient;
use App\Services\PaymentService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

#[Signature('orders:sync-pending')]
#[Description('Sinkronkan status order Midtrans yang masih menunggu pembayaran')]
class OrdersSyncPending extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(MidtransClient $client, PaymentService $payments): int
    {
        if (! $client->isConfigured()) {
            $this->warn('Kredensial Midtrans belum dikonfigurasi. Sinkronisasi dilewati.');

            return self::SUCCESS;
        }

        $orders = Order::query()
            ->pending()
            ->whereNotNull('snap_token')
            ->where('created_at', '>=', now()->subDays(2))
            ->orderBy('id')
            ->limit(100)
            ->get();

        $paid = 0;
        $checked = 0;

        foreach ($orders as $order) {
            $checked++;

            try {
                $payload = $client->getTransactionStatus($order->order_id);
            } catch (Throwable $e) {
                Log::warning('Gagal sinkronisasi status order Midtrans.', [
                    'order_id' => $order->order_id,
                    'exception' => $e,
                ]);

                continue;
            }

            $grossAmount = (int) round((float) ($payload['gross_amount'] ?? 0));

            if ($grossAmount !== $order->gross_amount) {
                Log::error('Nominal status Midtrans tidak cocok saat sinkronisasi.', [
                    'order_id' => $order->order_id,
                    'expected' => $order->gross_amount,
                    'received' => $payload['gross_amount'] ?? null,
                ]);

                continue;
            }

            $updated = $payments->apply($order, $payload);

            if ($updated->isPaid()) {
                $paid++;
            }
        }

        $this->info("{$checked} order diperiksa, {$paid} order menjadi lunas.");

        return self::SUCCESS;
    }
}
