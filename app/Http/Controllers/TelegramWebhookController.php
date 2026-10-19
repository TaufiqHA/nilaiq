<?php

namespace App\Http\Controllers;

use App\Services\Telegram\TelegramUpdateProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramWebhookController extends Controller
{
    /**
     * Handle an update sent by Telegram.
     */
    public function handle(Request $request, TelegramUpdateProcessor $processor): JsonResponse
    {
        $secret = (string) config('services.telegram.webhook_secret');
        $provided = (string) $request->header('X-Telegram-Bot-Api-Secret-Token', '');

        if ($secret === '' || ! hash_equals($secret, $provided)) {
            abort(403, 'Invalid Telegram webhook secret.');
        }

        try {
            $processor->process($request->all());
        } catch (Throwable $e) {
            Log::error('Telegram webhook error: '.$e->getMessage(), [
                'exception' => $e,
            ]);
        }

        // Always return 200 so Telegram does not retry indefinitely. The
        // processor logs failures and removes the dedupe marker for retries.
        return response()->json(['ok' => true]);
    }
}
