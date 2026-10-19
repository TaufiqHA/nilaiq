<?php

namespace App\Services\Telegram;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Thin wrapper around the Telegram Bot API.
 */
class TelegramClient
{
    /**
     * Send a text message to a chat.
     *
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    public function sendMessage(int|string $chatId, string $text, array $extra = []): array
    {
        return $this->call('sendMessage', array_merge([
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ], $extra));
    }

    /**
     * Answer a callback query so Telegram stops the loading indicator.
     *
     * @return array<string, mixed>
     */
    public function answerCallbackQuery(string $callbackQueryId, ?string $text = null): array
    {
        return $this->call('answerCallbackQuery', array_filter([
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
        ]));
    }

    /**
     * Fetch pending updates (used by the local polling command).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getUpdates(int $offset = 0, int $timeout = 30): array
    {
        return $this->call('getUpdates', array_filter([
            'offset' => $offset,
            'timeout' => $timeout,
            'allowed_updates' => ['message', 'callback_query'],
        ]));
    }

    /**
     * Register the webhook URL with Telegram.
     */
    public function setWebhook(string $url, ?string $secretToken = null): array
    {
        return $this->call('setWebhook', array_filter([
            'url' => $url,
            'secret_token' => $secretToken,
            'allowed_updates' => ['message', 'callback_query'],
        ]));
    }

    /**
     * Remove the registered webhook.
     */
    public function deleteWebhook(): array
    {
        return $this->call('deleteWebhook');
    }

    /**
     * Get the bot account information.
     */
    public function getMe(): array
    {
        return $this->call('getMe');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function call(string $method, array $payload = []): array
    {
        $token = (string) config('services.telegram.token');

        if ($token === '') {
            throw new RuntimeException('TELEGRAM_BOT_TOKEN belum dikonfigurasi.');
        }

        $response = Http::timeout(40)
            ->acceptJson()
            ->asJson()
            ->post("https://api.telegram.org/bot{$token}/{$method}", $payload);

        return $this->decodeResponse($method, $response);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeResponse(string $method, Response $response): array
    {
        $body = $response->json();

        if ($response->failed() || ! is_array($body) || ($body['ok'] ?? false) !== true) {
            $description = is_array($body) ? ($body['description'] ?? null) : null;

            throw new RuntimeException(
                'Telegram API '.$method.' gagal: '.($description ?? 'HTTP '.$response->status())
            );
        }

        $result = $body['result'] ?? [];

        return is_array($result) ? $result : [];
    }
}
