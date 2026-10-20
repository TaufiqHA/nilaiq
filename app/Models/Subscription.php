<?php

namespace App\Models;

use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'plan_id',
    'source',
    'status',
    'starts_at',
    'ends_at',
    'trial_ends_at',
    'canceled_at',
])]
class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    public const STATUS_TRIALING = 'trialing';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_EXPIRED = 'expired';

    public const STATUS_CANCELED = 'canceled';

    public const SOURCE_TRIAL = 'trial';

    public const SOURCE_PURCHASE = 'purchase';

    public const SOURCE_LEGACY = 'legacy';

    public const SOURCE_ADMIN = 'admin';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'trial_ends_at' => 'datetime',
            'canceled_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan of the subscription.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Limit the query to subscriptions that currently grant access.
     */
    public function scopeUsable(Builder $query): Builder
    {
        return $query
            ->whereIn('status', [self::STATUS_TRIALING, self::STATUS_ACTIVE])
            ->where(function (Builder $query): void {
                $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
            });
    }

    /**
     * Determine whether the subscription currently grants access.
     */
    public function isUsable(): bool
    {
        return in_array($this->status, [self::STATUS_TRIALING, self::STATUS_ACTIVE], true)
            && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    /**
     * Determine whether the subscription never expires.
     */
    public function isLifetime(): bool
    {
        return $this->ends_at === null;
    }

    /**
     * Determine whether the subscription is still in its trial period.
     */
    public function isOnTrial(): bool
    {
        return $this->status === self::STATUS_TRIALING;
    }

    /**
     * Get the remaining days before the subscription expires.
     */
    public function daysLeft(): int
    {
        if ($this->ends_at === null) {
            return 0;
        }

        return max(0, (int) ceil(now()->diffInHours($this->ends_at, false) / 24));
    }

    /**
     * Get a human readable status label.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_TRIALING => 'Trial',
            self::STATUS_ACTIVE => $this->isLifetime() ? 'Aktif Selamanya' : 'Aktif',
            self::STATUS_CANCELED => 'Dibatalkan',
            default => 'Kedaluwarsa',
        };
    }
}
