<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
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
            'subscription_id' => null,
            'order_id' => 'NQ-'.fake()->unique()->numerify('##########'),
            'gross_amount' => 150000,
            'status' => Order::STATUS_PENDING,
            'snap_token' => null,
            'snap_redirect_url' => null,
            'payment_type' => null,
            'transaction_id' => null,
            'fraud_status' => null,
            'paid_at' => null,
            'expires_at' => now()->addDay(),
            'raw_payload' => null,
        ];
    }

    /**
     * Indicate that the order has been paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Order::STATUS_SETTLEMENT,
            'payment_type' => 'bank_transfer',
            'transaction_id' => fake()->uuid(),
            'paid_at' => now(),
        ]);
    }
}
