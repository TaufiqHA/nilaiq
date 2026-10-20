<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plan_id' => null,
            'source' => Subscription::SOURCE_PURCHASE,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
            'trial_ends_at' => null,
            'canceled_at' => null,
        ];
    }

    /**
     * Indicate that the subscription is a three day trial.
     */
    public function trial(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => Subscription::SOURCE_TRIAL,
            'status' => Subscription::STATUS_TRIALING,
            'starts_at' => now(),
            'ends_at' => now()->addDays(3),
            'trial_ends_at' => now()->addDays(3),
        ]);
    }

    /**
     * Indicate that the subscription is a legacy lifetime subscription.
     */
    public function lifetime(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => Subscription::SOURCE_LEGACY,
            'status' => Subscription::STATUS_ACTIVE,
            'starts_at' => now(),
            'ends_at' => null,
            'trial_ends_at' => null,
        ]);
    }

    /**
     * Indicate that the subscription has ended.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Subscription::STATUS_EXPIRED,
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->subDay(),
            'trial_ends_at' => null,
        ]);
    }
}
