<?php

namespace App\Console\Commands;

use App\Services\Telegram\TelegramClient;
use Illuminate\Console\Command;
use Throwable;

class TelegramSetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {url? : URL webhook publik}';

    protected $description = 'Daftarkan webhook Telegram untuk bot NilaiQ';

    public function handle(TelegramClient $client): int
    {
        $url = $this->argument('url') ?: url('/api/telegram/webhook');
        $secret = (string) config('services.telegram.webhook_secret');

        if ((string) config('services.telegram.token') === '') {
            $this->error('TELEGRAM_BOT_TOKEN belum diisi di .env.');

            return self::FAILURE;
        }

        if ($secret === '') {
            $this->error('TELEGRAM_WEBHOOK_SECRET belum diisi di .env.');

            return self::FAILURE;
        }

        try {
            $client->setWebhook($url, $secret);
            $this->info('Webhook Telegram berhasil didaftarkan: '.$url);

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
