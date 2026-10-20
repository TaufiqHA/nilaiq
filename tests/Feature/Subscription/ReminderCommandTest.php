<?php

namespace Tests\Feature\Subscription;

use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionReminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReminderCommandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test reminders are sent once for a trial ending in three days.
     */
    public function test_trial_reminder_is_sent_once(): void
    {
        Notification::fake();

        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        Subscription::factory()->trial()->create([
            'user_id' => $user->id,
            'ends_at' => now()->addDay(),
            'trial_ends_at' => now()->addDay(),
        ]);

        $this->artisan('subscriptions:send-reminders')->assertSuccessful();
        $this->artisan('subscriptions:send-reminders')->assertSuccessful();

        Notification::assertSentToTimes($user, SubscriptionReminder::class, 1);
    }

    /**
     * Test active subscriptions receive the three day reminder.
     */
    public function test_active_subscription_reminder_is_sent_before_expiry(): void
    {
        Notification::fake();

        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => Subscription::STATUS_ACTIVE,
            'ends_at' => now()->addDays(3),
        ]);

        $this->artisan('subscriptions:send-reminders')->assertSuccessful();

        Notification::assertSentToTimes($user, SubscriptionReminder::class, 1);
    }

    /**
     * Test ended subscriptions are flagged as expired.
     */
    public function test_expire_command_marks_ended_subscriptions(): void
    {
        /** @var User $user */
        $user = User::factory()->withoutSubscription()->create();
        $subscription = Subscription::factory()->create([
            'user_id' => $user->id,
            'status' => Subscription::STATUS_ACTIVE,
            'ends_at' => now()->subDay(),
        ]);

        $this->artisan('subscriptions:expire')->assertSuccessful();

        $this->assertSame(Subscription::STATUS_EXPIRED, $subscription->fresh()->status);
        $this->assertFalse($user->fresh()->hasAppAccess());
    }
}
