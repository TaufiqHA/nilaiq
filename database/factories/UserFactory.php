<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The last user created by the factory, used to scope nested test data.
     */
    public static ?int $lastCreatedId = null;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'mapel',
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Give every generated user lifetime access unless a test opts out.
     *
     * Existing feature tests create users without thinking about billing,
     * mirroring the legacy accounts that keep access forever.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            static::$lastCreatedId = $user->id;

            if ($user->subscriptions()->exists()) {
                return;
            }

            Subscription::factory()->lifetime()->create(['user_id' => $user->id]);
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a user without any subscription entitlement.
     */
    public function withoutSubscription(): static
    {
        return $this->afterCreating(function (User $user): void {
            $user->subscriptions()->delete();
            $user->forgetSubscriptionCache();
        });
    }
}
