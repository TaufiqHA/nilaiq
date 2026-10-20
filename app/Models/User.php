<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    private ?Subscription $cachedSubscription = null;

    private bool $subscriptionLoaded = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get every subscription owned by the user.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get every order placed by the user.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the subscription that currently grants access, if any.
     */
    public function currentSubscription(): ?Subscription
    {
        if (! $this->subscriptionLoaded) {
            $this->cachedSubscription = $this->relationLoaded('subscriptions')
                ? $this->subscriptions
                    ->filter(fn (Subscription $subscription): bool => $subscription->isUsable())
                    ->sortByDesc(fn (Subscription $subscription): int => $subscription->ends_at?->getTimestamp() ?? PHP_INT_MAX)
                    ->first()
                : $this->subscriptions()
                    ->usable()
                    ->orderByRaw('CASE WHEN ends_at IS NULL THEN 0 ELSE 1 END')
                    ->orderByDesc('ends_at')
                    ->first();

            $this->subscriptionLoaded = true;
        }

        return $this->cachedSubscription;
    }

    /**
     * Clear the cached subscription after the entitlement changes.
     */
    public function forgetSubscriptionCache(): void
    {
        $this->cachedSubscription = null;
        $this->subscriptionLoaded = false;
    }

    /**
     * Determine whether the user may access the application features.
     */
    public function hasAppAccess(): bool
    {
        return $this->role === 'admin' || $this->currentSubscription() !== null;
    }

    /**
     * Determine whether the user is currently in a trial period.
     */
    public function isOnTrial(): bool
    {
        return $this->currentSubscription()?->isOnTrial() ?? false;
    }

    /**
     * Get the remaining days of the current subscription.
     */
    public function subscriptionDaysLeft(): int
    {
        return $this->currentSubscription()?->daysLeft() ?? 0;
    }

    /**
     * Get a human readable label for the current subscription status.
     */
    public function subscriptionStatusLabel(): string
    {
        return $this->currentSubscription()?->statusLabel() ?? 'Tidak Aktif';
    }
}
