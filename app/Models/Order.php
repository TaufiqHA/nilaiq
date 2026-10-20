<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'plan_id',
    'subscription_id',
    'order_id',
    'gross_amount',
    'status',
    'snap_token',
    'snap_redirect_url',
    'payment_type',
    'transaction_id',
    'fraud_status',
    'paid_at',
    'expires_at',
    'raw_payload',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_SETTLEMENT = 'settlement';

    public const STATUS_CAPTURE = 'capture';

    public const STATUS_DENY = 'deny';

    public const STATUS_CANCEL = 'cancel';

    public const STATUS_EXPIRE = 'expire';

    public const STATUS_REFUND = 'refund';

    public const STATUS_FAILURE = 'failure';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gross_amount' => 'integer',
            'paid_at' => 'datetime',
            'expires_at' => 'datetime',
            'raw_payload' => 'array',
        ];
    }

    /**
     * Get the user that placed the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the purchased plan.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the subscription created by the order.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Limit the query to orders that are still waiting for payment.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Determine whether the order has been paid.
     */
    public function isPaid(): bool
    {
        return in_array($this->status, [self::STATUS_SETTLEMENT, self::STATUS_CAPTURE], true);
    }

    /**
     * Determine whether the order can still be paid.
     */
    public function isPayable(): bool
    {
        return $this->status === self::STATUS_PENDING
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Get a human readable label for the order status.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Pembayaran',
            self::STATUS_SETTLEMENT => 'Lunas',
            self::STATUS_CAPTURE => 'Lunas',
            self::STATUS_DENY => 'Ditolak',
            self::STATUS_CANCEL => 'Dibatalkan',
            self::STATUS_EXPIRE => 'Kedaluwarsa',
            self::STATUS_REFUND => 'Dikembalikan',
            default => 'Gagal',
        };
    }
}
