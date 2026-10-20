<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('subscriptions:expire')]
#[Description('Tandai langganan yang sudah melewati tanggal berakhir sebagai kedaluwarsa')]
class SubscriptionsExpire extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(SubscriptionService $subscriptions): int
    {
        $updated = $subscriptions->expireEndedSubscriptions();

        $this->info("{$updated} langganan ditandai kedaluwarsa.");

        return self::SUCCESS;
    }
}
