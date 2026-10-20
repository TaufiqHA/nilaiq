<?php

namespace App\Services\Midtrans;

use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MidtransClient
{
    /**
     * Create a Snap transaction and return its token and redirect URL.
     *
     * @return array{token: string, redirect_url: string}
     */
    public function createSnapTransaction(Order $order, User $user, ?Plan $plan = null): array
    {
        $plan ??= $order->plan;

        $payload = [
            'transaction_details' => [
                'order_id' => $order->order_id,
                'gross_amount' => $order->gross_amount,
            ],
            'item_details' => [[
                'id' => $plan?->code ?? Plan::ANNUAL_CODE,
                'price' => $order->gross_amount,
                'quantity' => 1,
                'name' => Str::limit($plan?->name ?? 'Langganan NilaiQ', 50, ''),
            ]],
            'customer_details' => [
                'first_name' => Str::limit($user->name, 50, ''),
                'email' => $user->email,
            ],
            'callbacks' => [
                'finish' => route('billing.finish'),
            ],
        ];

        $response = $this->request()
            ->post('/snap/v1/transactions', $payload)
            ->throw();

        /** @var array{token?: string, redirect_url?: string} $data */
        $data = $response->json();

        if (empty($data['token']) || empty($data['redirect_url'])) {
            throw new RuntimeException('Midtrans tidak mengembalikan token pembayaran.');
        }

        return [
            'token' => $data['token'],
            'redirect_url' => $data['redirect_url'],
        ];
    }

    /**
     * Fetch the latest transaction status from Midtrans.
     *
     * @return array<string, mixed>
     */
    public function getTransactionStatus(string $orderId): array
    {
        $response = $this->request($this->apiBaseUrl())
            ->get('/v2/'.rawurlencode($orderId).'/status')
            ->throw();

        /** @var array<string, mixed> $data */
        $data = $response->json();

        return $data;
    }

    /**
     * Verify the signature of an HTTP notification from Midtrans.
     *
     * @param  array<string, mixed>  $payload
     */
    public function verifySignature(array $payload): bool
    {
        $serverKey = (string) config('services.midtrans.server_key');

        if ($serverKey === '' || ! isset($payload['order_id'], $payload['status_code'], $payload['gross_amount'], $payload['signature_key'])) {
            return false;
        }

        $expected = hash('sha512',
            (string) $payload['order_id'].
            (string) $payload['status_code'].
            (string) $payload['gross_amount'].
            $serverKey
        );

        return hash_equals($expected, (string) $payload['signature_key']);
    }

    /**
     * Determine whether Midtrans credentials are configured.
     */
    public function isConfigured(): bool
    {
        return (string) config('services.midtrans.server_key') !== '';
    }

    /**
     * Build an authenticated request to the Midtrans API.
     */
    private function request(?string $baseUrl = null): PendingRequest
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Kredensial Midtrans belum dikonfigurasi.');
        }

        return Http::baseUrl($baseUrl ?? $this->snapBaseUrl())
            ->withBasicAuth((string) config('services.midtrans.server_key'), '')
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            ->connectTimeout(5)
            ->retry(2, 200);
    }

    /**
     * Get the Snap base URL for the active environment.
     */
    private function snapBaseUrl(): string
    {
        return config('services.midtrans.is_production')
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';
    }

    /**
     * Get the API base URL used for transaction status requests.
     */
    private function apiBaseUrl(): string
    {
        return config('services.midtrans.is_production')
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }
}
