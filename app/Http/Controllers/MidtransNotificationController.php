<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Midtrans\MidtransClient;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransNotificationController extends Controller
{
    /**
     * Handle an HTTP notification sent by Midtrans.
     */
    public function __invoke(
        Request $request,
        MidtransClient $client,
        PaymentService $payments,
    ): JsonResponse {
        $payload = $request->all();

        if (! $client->verifySignature($payload)) {
            Log::warning('Notifikasi Midtrans dengan signature tidak valid.', [
                'order_id' => $payload['order_id'] ?? null,
                'ip' => $request->ip(),
            ]);

            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $order = Order::query()
            ->where('order_id', (string) ($payload['order_id'] ?? ''))
            ->first();

        if ($order === null) {
            Log::warning('Notifikasi Midtrans untuk order yang tidak dikenal.', [
                'order_id' => $payload['order_id'] ?? null,
            ]);

            return response()->json(['message' => 'Order not found.'], 404);
        }

        $grossAmount = (int) round((float) ($payload['gross_amount'] ?? 0));

        if ($grossAmount !== $order->gross_amount) {
            Log::error('Nominal notifikasi Midtrans tidak cocok.', [
                'order_id' => $order->order_id,
                'expected' => $order->gross_amount,
                'received' => $payload['gross_amount'] ?? null,
            ]);

            return response()->json(['message' => 'Amount mismatch.'], 400);
        }

        $payments->apply($order, $payload);

        return response()->json(['ok' => true]);
    }
}
