<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Start the free trial for a newly registered user.
     */
    public function startTrial(User $user, ?Plan $plan = null): Subscription
    {
        $trialDays = (int) config('subscription.trial_days', 3);
        $endsAt = now()->addDays($trialDays);

        return $user->subscriptions()->create([
            'plan_id' => $plan?->id,
            'source' => Subscription::SOURCE_TRIAL,
            'status' => Subscription::STATUS_TRIALING,
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'trial_ends_at' => $endsAt,
        ]);
    }

    /**
     * Activate or extend a subscription after a successful payment.
     *
     * The new period starts after the current entitlement so renewals never
     * lose remaining days. Buying during a trial keeps the trial days too.
     */
    public function activate(User $user, ?Plan $plan = null, string $source = Subscription::SOURCE_PURCHASE): Subscription
    {
        return DB::transaction(function () use ($user, $plan, $source): Subscription {
            /** @var User $lockedUser */
            $lockedUser = User::query()->lockForUpdate()->findOrFail($user->id);

            $current = $lockedUser->subscriptions()
                ->usable()
                ->orderByRaw('CASE WHEN ends_at IS NULL THEN 0 ELSE 1 END')
                ->orderByDesc('ends_at')
                ->first();

            if ($current?->isLifetime()) {
                return $current;
            }

            $baseDate = $current?->ends_at !== null && $current->ends_at->isFuture()
                ? $current->ends_at->copy()
                : now();

            $duration = $plan?->duration_days ?? 365;
            $endsAt = $baseDate->addDays($duration);

            if ($current !== null) {
                $current->update([
                    'plan_id' => $plan?->id ?? $current->plan_id,
                    'source' => $source,
                    'status' => Subscription::STATUS_ACTIVE,
                    'ends_at' => $endsAt,
                ]);

                return $current->refresh();
            }

            return $lockedUser->subscriptions()->create([
                'plan_id' => $plan?->id,
                'source' => $source,
                'status' => Subscription::STATUS_ACTIVE,
                'starts_at' => now(),
                'ends_at' => $endsAt,
            ]);
        });
    }

    /**
     * Give a user lifetime access, used by administrators.
     */
    public function grantLifetime(User $user): Subscription
    {
        $user->subscriptions()->update([
            'status' => Subscription::STATUS_EXPIRED,
        ]);

        return $user->subscriptions()->create([
            'source' => Subscription::SOURCE_ADMIN,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => null,
        ]);
    }

    /**
     * Revoke every active entitlement of a user.
     */
    public function revoke(User $user): void
    {
        $user->subscriptions()
            ->whereIn('status', [Subscription::STATUS_TRIALING, Subscription::STATUS_ACTIVE])
            ->update([
                'status' => Subscription::STATUS_CANCELED,
                'canceled_at' => now(),
            ]);
    }

    /**
     * Mark subscriptions as expired once their end date has passed.
     */
    public function expireEndedSubscriptions(): int
    {
        return Subscription::query()
            ->whereIn('status', [Subscription::STATUS_TRIALING, Subscription::STATUS_ACTIVE])
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->update(['status' => Subscription::STATUS_EXPIRED]);
    }
}
