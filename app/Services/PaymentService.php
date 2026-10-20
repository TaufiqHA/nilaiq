<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(private SubscriptionService $subscriptions) {}

    /**
     * Apply a Midtrans status payload to an order idempotently.
     *
     * Repeated or out-of-order notifications must never grant access twice
     * or downgrade an order that has already been paid.
     *
     * @param  array<string, mixed>  $payload
     */
    public function apply(Order $order, array $payload): Order
    {
        return DB::transaction(function () use ($order, $payload): Order {
            /** @var Order $locked */
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);

            $wasPaid = $locked->isPaid();
            $midtransStatus = strtolower((string) ($payload['transaction_status'] ?? ''));
            $fraudStatus = strtolower((string) ($payload['fraud_status'] ?? ''));
            $isPaid = $this->isPaidStatus($midtransStatus, $fraudStatus);

            $attributes = [
                'payment_type' => $payload['payment_type'] ?? $locked->payment_type,
                'transaction_id' => $payload['transaction_id'] ?? $locked->transaction_id,
                'fraud_status' => $fraudStatus !== '' ? $fraudStatus : $locked->fraud_status,
                'raw_payload' => $payload,
            ];

            if (! $wasPaid) {
                $attributes['status'] = $this->mapStatus($midtransStatus, $fraudStatus);
            }

            if ($isPaid && ! $wasPaid) {
                $attributes['status'] = $midtransStatus === Order::STATUS_CAPTURE
                    ? Order::STATUS_CAPTURE
                    : Order::STATUS_SETTLEMENT;
                $attributes['paid_at'] = now();
            }

            $locked->update($attributes);

            if ($isPaid && ! $wasPaid) {
                $subscription = $this->subscriptions->activate($locked->user, $locked->plan);
                $locked->update(['subscription_id' => $subscription->id]);
            }

            if (! $isPaid && $midtransStatus !== '' && ! $wasPaid) {
                Log::info('Midtrans order status updated.', [
                    'order_id' => $locked->order_id,
                    'status' => $attributes['status'] ?? $locked->status,
                ]);
            }

            return $locked->refresh();
        });
    }

    /**
     * Determine whether a Midtrans status means the order is paid.
     */
    private function isPaidStatus(string $status, string $fraudStatus): bool
    {
        if ($status === Order::STATUS_SETTLEMENT) {
            return true;
        }

        return $status === Order::STATUS_CAPTURE && $fraudStatus === 'accept';
    }

    /**
     * Map a Midtrans transaction status to the local order status.
     */
    private function mapStatus(string $status, string $fraudStatus): string
    {
        return match ($status) {
            Order::STATUS_SETTLEMENT => Order::STATUS_SETTLEMENT,
            Order::STATUS_CAPTURE => $fraudStatus === 'accept'
                ? Order::STATUS_CAPTURE
                : Order::STATUS_PENDING,
            Order::STATUS_PENDING => Order::STATUS_PENDING,
            Order::STATUS_DENY => Order::STATUS_DENY,
            Order::STATUS_CANCEL => Order::STATUS_CANCEL,
            Order::STATUS_EXPIRE => Order::STATUS_EXPIRE,
            'refund', 'partial_refund' => Order::STATUS_REFUND,
            default => Order::STATUS_FAILURE,
        };
    }
}
