<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\CheckoutService;
use App\Services\Midtrans\MidtransClient;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class BillingController extends Controller
{
    /**
     * Show the subscription status and payment history.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $orders = $user->orders()->with('plan')->latest()->limit(20)->get();

        return view('auth.billing', [
            'user' => $user,
            'subscription' => $user->currentSubscription(),
            'orders' => $orders,
            'pendingOrder' => $orders->first(
                fn (Order $order): bool => $order->isPayable() && $order->snap_token !== null
            ),
        ]);
    }

    /**
     * Create (or reuse) an order and send the user to Midtrans Snap.
     */
    public function checkout(Request $request, CheckoutService $checkout): RedirectResponse|JsonResponse
    {
        try {
            $order = $checkout->createOrder($request->user());
        } catch (RuntimeException $e) {
            return $this->billingError($request, $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'token' => $order->snap_token,
                'redirect_url' => $order->snap_redirect_url,
            ]);
        }

        return redirect()->away($order->snap_redirect_url);
    }

    /**
     * Handle the Midtrans finish redirect and refresh the order status.
     */
    public function finish(Request $request, MidtransClient $client, PaymentService $payments): RedirectResponse
    {
        $order = $request->user()
            ->orders()
            ->where('order_id', (string) $request->query('order_id', ''))
            ->first();

        if ($order === null) {
            return redirect()->route('billing.index')->with('error', 'Transaksi tidak ditemukan.');
        }

        if (! $order->isPaid()) {
            $this->syncOrder($order, $client, $payments);
        }

        $order->refresh();

        return redirect()->route('billing.index')->with(
            $order->isPaid() ? 'success' : 'info',
            $order->isPaid()
                ? 'Pembayaran berhasil. Langganan Anda sudah aktif.'
                : 'Pembayaran sedang diproses. Status akan diperbarui otomatis setelah pembayaran dikonfirmasi.'
        );
    }

    /**
     * Manually refresh the latest pending order status.
     */
    public function sync(Request $request, MidtransClient $client, PaymentService $payments): RedirectResponse
    {
        $order = $request->user()->orders()->pending()->latest()->first();

        if ($order === null) {
            return redirect()->route('billing.index')->with('info', 'Tidak ada transaksi yang menunggu pembayaran.');
        }

        $this->syncOrder($order, $client, $payments);
        $order->refresh();

        return redirect()->route('billing.index')->with(
            $order->isPaid() ? 'success' : 'info',
            $order->isPaid()
                ? 'Pembayaran berhasil. Langganan Anda sudah aktif.'
                : 'Pembayaran belum dikonfirmasi. Silakan selesaikan pembayaran terlebih dahulu.'
        );
    }

    /**
     * Pull the latest Midtrans status for an order.
     */
    private function syncOrder(Order $order, MidtransClient $client, PaymentService $payments): void
    {
        if (! $client->isConfigured()) {
            return;
        }

        try {
            $payload = $client->getTransactionStatus($order->order_id);
        } catch (Throwable $e) {
            Log::warning('Gagal mengambil status transaksi Midtrans.', [
                'order_id' => $order->order_id,
                'exception' => $e,
            ]);

            return;
        }

        if ((int) round((float) ($payload['gross_amount'] ?? 0)) !== $order->gross_amount) {
            Log::error('Nominal status Midtrans tidak cocok.', [
                'order_id' => $order->order_id,
                'expected' => $order->gross_amount,
                'received' => $payload['gross_amount'] ?? null,
            ]);

            return;
        }

        $payments->apply($order, $payload);
    }

    /**
     * Return the appropriate error response for checkout failures.
     */
    private function billingError(Request $request, string $message): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return back()->with('error', $message);
    }
}
