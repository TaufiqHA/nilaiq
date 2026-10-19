<?php

namespace App\Console\Commands;

use App\Services\Telegram\TelegramClient;
use Illuminate\Console\Command;
use Throwable;

class TelegramDeleteWebhook extends Command
{
    protected $signature = 'telegram:delete-webhook';

    protected $description = 'Hapus webhook Telegram bot NilaiQ';

    public function handle(TelegramClient $client): int
    {
        try {
            $client->deleteWebhook();
            $this->info('Webhook Telegram berhasil dihapus.');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
