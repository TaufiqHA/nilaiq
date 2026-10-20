<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\TelegramLink;
use App\Notifications\SubscriptionReminder;
use App\Services\Telegram\TelegramClient;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

#[Signature('subscriptions:send-reminders')]
#[Description('Kirim pengingat trial dan langganan yang akan berakhir')]
class SubscriptionsSendReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(TelegramClient $telegram): int
    {
        $reminderDays = (array) config('subscription.reminder_days', [7, 3, 1]);
        $sent = 0;

        foreach ($reminderDays as $days) {
            $targetDate = now()->addDays((int) $days)->toDateString();

            Subscription::query()
                ->with('user')
                ->whereIn('status', [Subscription::STATUS_TRIALING, Subscription::STATUS_ACTIVE])
                ->whereNotNull('ends_at')
                ->whereDate('ends_at', $targetDate)
                ->orderBy('id')
                ->chunkById(100, function ($subscriptions) use ($days, $telegram, &$sent): void {
                    foreach ($subscriptions as $subscription) {
                        // A three day trial should only receive the final reminder.
                        if ($subscription->isOnTrial() && (int) $days > 1) {
                            continue;
                        }

                        if ($this->sendReminder($subscription, (int) $days, $telegram)) {
                            $sent++;
                        }
                    }
                });
        }

        $this->info("{$sent} pengingat langganan dikirim.");

        return self::SUCCESS;
    }

    /**
     * Send the e-mail and Telegram reminder for a single subscription.
     */
    private function sendReminder(Subscription $subscription, int $days, TelegramClient $telegram): bool
    {
        $user = $subscription->user;

        if ($user === null) {
            return false;
        }

        $cacheKey = "subscription-reminder:{$subscription->id}:{$days}";

        if (! Cache::add($cacheKey, true, now()->addDay())) {
            return false;
        }

        $isTrial = $subscription->isOnTrial();
        $title = $isTrial
            ? "Trial NilaiQ berakhir dalam {$days} hari"
            : "Langganan NilaiQ berakhir dalam {$days} hari";

        $message = $isTrial
            ? "Masa trial gratis Anda akan berakhir dalam {$days} hari. Berlangganan sekarang agar akses ke seluruh fitur NilaiQ tidak terputus."
            : "Langganan tahunan Anda akan berakhir dalam {$days} hari. Perpanjang sekarang agar data dan fitur tetap dapat diakses.";

        try {
            $user->notify(new SubscriptionReminder($title, $message, route('billing.index'), $days));
        } catch (Throwable $e) {
            Log::warning('Gagal mengirim email pengingat langganan.', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'exception' => $e,
            ]);
        }

        $link = TelegramLink::query()->where('user_id', $user->id)->first();

        if ($link !== null) {
            try {
                $telegram->sendMessage(
                    $link->chat_id,
                    "<b>{$title}</b>\n\n{$message}\n\nKelola langganan: ".route('billing.index'),
                );
            } catch (Throwable $e) {
                Log::warning('Gagal mengirim pengingat langganan via Telegram.', [
                    'user_id' => $user->id,
                    'exception' => $e,
                ]);
            }
        }

        return true;
    }
}
