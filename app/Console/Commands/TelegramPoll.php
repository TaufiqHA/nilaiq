<?php

namespace App\Console\Commands;

use App\Services\Telegram\TelegramClient;
use App\Services\Telegram\TelegramUpdateProcessor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramPoll extends Command
{
    protected $signature = 'telegram:poll {--timeout=30 : Long polling timeout dalam detik}';

    protected $description = 'Jalankan bot NilaiQ dengan long polling (untuk development lokal)';

    public function handle(TelegramClient $client, TelegramUpdateProcessor $processor): int
    {
        if ((string) config('services.telegram.token') === '') {
            $this->error('TELEGRAM_BOT_TOKEN belum diisi di .env.');

            return self::FAILURE;
        }

        $offset = 0;
        $timeout = max(0, min(50, (int) $this->option('timeout')));

        $this->info('Polling Telegram dimulai. Tekan Ctrl+C untuk berhenti.');

        while (true) {
            try {
                $updates = $client->getUpdates($offset, $timeout);
            } catch (Throwable $e) {
                $this->error($e->getMessage());
                sleep(5);

                continue;
            }

            foreach ($updates as $update) {
                $updateId = (int) ($update['update_id'] ?? 0);
                $offset = max($offset, $updateId + 1);

                try {
                    $processor->process($update);
                } catch (Throwable $e) {
                    Log::error('Telegram polling error: '.$e->getMessage(), [
                        'update_id' => $updateId,
                        'exception' => $e,
                    ]);
                    $this->error('Update '.$updateId.' gagal: '.$e->getMessage());
                }
            }
        }
    }
}
