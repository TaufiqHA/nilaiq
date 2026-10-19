<?php

namespace App\Console\Commands;

use App\Models\TelegramLink;
use App\Services\Telegram\TelegramClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramRemoveKeyboard extends Command
{
    protected $signature = 'telegram:remove-keyboard';

    protected $description = 'Hapus reply keyboard lama dari semua chat Telegram yang terhubung';

    public function handle(TelegramClient $client): int
    {
        if ((string) config('services.telegram.token') === '') {
            $this->error('TELEGRAM_BOT_TOKEN belum diisi di .env.');

            return self::FAILURE;
        }

        $sent = 0;
        $failed = 0;

        foreach (TelegramLink::query()->cursor() as $link) {
            try {
                $client->sendMessage(
                    $link->chat_id,
                    'Menu keyboard telah dihapus. Ketik /bantuan untuk melihat daftar perintah.',
                    ['reply_markup' => ['remove_keyboard' => true]]
                );

                $sent++;
            } catch (Throwable $e) {
                $failed++;

                Log::warning('Telegram remove keyboard failed: '.$e->getMessage(), [
                    'chat_id' => $link->chat_id,
                    'exception' => $e,
                ]);
            }
        }

        $this->info("Keyboard dihapus untuk {$sent} chat.");

        if ($failed > 0) {
            $this->warn("{$failed} chat gagal diproses. Lihat log untuk detail.");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
