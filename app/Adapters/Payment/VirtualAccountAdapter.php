<?php

namespace App\Adapters\Payment;

use App\Adapters\Contracts\PaymentAdapterInterface;
use Illuminate\Support\Facades\Http;

/**
 * Virtual Account adapter — Indonesia.
 *
 * Generate VA number per transaction. Customer transfer ke VA via ATM/iBanking.
 *
 * Compatible with:
 * - Midtrans Core API VA (BCA, Mandiri, BNI, BRI, Permata, CIMB)
 * - Xendit VA (semua bank)
 * - Doku VA
 * - Flip Big Flip VA
 * - OY Indonesia VA
 */
class VirtualAccountAdapter implements PaymentAdapterInterface
{
    private string $baseUrl;
    private string $apiKey;
    private string $createPath = '/v1/virtual_accounts';
    private string $defaultBank = 'BCA';

    public function configure(array $config): void
    {
        $this->baseUrl = rtrim($config['base_url'] ?? '', '/');
        $this->apiKey = $config['api_key'] ?? '';
        $this->createPath = $config['create_path'] ?? '/v1/virtual_accounts';
        $this->defaultBank = $config['default_bank'] ?? 'BCA';
    }

    public function test(): array
    {
        return ['ok' => !empty($this->baseUrl) && !empty($this->apiKey)];
    }

    public function createCharge(array $payload): array
    {
        $bank = $payload['bank_code'] ?? $this->defaultBank;
        $resp = $this->httpClient()->post($this->baseUrl . $this->createPath, [
            'external_id' => $payload['reference'],
            'bank_code' => $bank,
            'name' => $payload['customer_name'] ?? 'Customer',
            'expected_amount' => (int) $payload['amount'],
            'is_closed' => true,
            'expiration_date' => $payload['expires_at'] ?? now()->addDay()->toIso8601String(),
        ]);

        if (!$resp->ok()) {
            throw new \RuntimeException('VA creation failed: ' . $resp->body());
        }

        $data = $resp->json();
        return [
            'reference_id' => $data['id'] ?? $payload['reference'],
            'status' => 'pending',
            'va_number' => $data['account_number'] ?? null,
            'bank_code' => $bank,
            'expires_at' => $data['expiration_date'] ?? null,
            'raw' => $data,
        ];
    }

    public function parseWebhook(string $rawBody, array $headers): array
    {
        $data = json_decode($rawBody, true) ?: [];
        return [
            'reference_id' => $data['external_id'] ?? $data['reference_id'] ?? null,
            'status' => strtoupper($data['status'] ?? '') === 'COMPLETED' ? 'paid' : 'pending',
            'amount' => $data['amount'] ?? null,
            'currency' => 'IDR',
            'raw' => $data,
        ];
    }

    public function getStatus(string $referenceId): array
    {
        $resp = $this->httpClient()->get($this->baseUrl . $this->createPath . '/' . $referenceId);
        return ['reference_id' => $referenceId, 'status' => $resp->json('status', 'unknown'), 'raw' => $resp->json()];
    }

    public function refund(string $referenceId, ?float $amount = null): array
    {
        return ['ok' => false, 'message' => 'VA refund manual via bank reversal'];
    }

    public function supportedMethods(): array
    {
        return ['va_bca', 'va_mandiri', 'va_bni', 'va_bri', 'va_permata', 'va_cimb', 'va_danamon', 'va_bsi'];
    }

    private function httpClient()
    {
        return Http::timeout(30)->acceptJson()->asJson()->withBasicAuth($this->apiKey, '');
    }
}
