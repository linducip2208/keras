<?php

namespace App\Adapters\Payment;

use App\Adapters\Contracts\PaymentAdapterInterface;
use Illuminate\Support\Facades\Http;

/**
 * Embed-based checkout (form di-render di halaman tenant, no redirect).
 *
 * Compatible with:
 * - Midtrans Snap.js Embed     (Indonesia)
 * - Stripe Elements / Payment Intent (International)
 * - Xendit Direct API           (Indonesia)
 * - Adyen Drop-in              (International)
 *
 * Returns client_secret / token untuk frontend (JS SDK) render form.
 */
class EmbedCheckoutAdapter implements PaymentAdapterInterface
{
    private string $baseUrl;
    private string $apiKey;
    private string $publishableKey = '';
    private string $intentPath = '/v1/payment_intents';

    public function configure(array $config): void
    {
        $this->baseUrl = rtrim($config['base_url'] ?? '', '/');
        $this->apiKey = $config['api_key'] ?? '';
        $this->publishableKey = $config['publishable_key'] ?? '';
        $this->intentPath = $config['intent_path'] ?? '/v1/payment_intents';
    }

    public function test(): array
    {
        $resp = $this->httpClient()->get($this->baseUrl);
        return ['ok' => $resp->status() < 500];
    }

    public function createCharge(array $payload): array
    {
        $resp = $this->httpClient()->asForm()->post($this->baseUrl . $this->intentPath, [
            'amount' => (int) round($payload['amount'] * ($payload['currency'] === 'IDR' ? 1 : 100)),
            'currency' => strtolower($payload['currency'] ?? 'usd'),
            'description' => $payload['description'] ?? null,
            'receipt_email' => $payload['customer_email'] ?? null,
            'metadata[reference]' => $payload['reference'],
        ]);

        if (!$resp->ok()) {
            throw new \RuntimeException('Embed intent failed: ' . $resp->body());
        }

        $data = $resp->json();
        return [
            'reference_id' => $data['id'] ?? $payload['reference'],
            'status' => $data['status'] ?? 'pending',
            'client_secret' => $data['client_secret'] ?? null,
            'publishable_key' => $this->publishableKey,
            'raw' => $data,
        ];
    }

    public function parseWebhook(string $rawBody, array $headers): array
    {
        $data = json_decode($rawBody, true) ?: [];
        $type = $data['type'] ?? '';
        $object = $data['data']['object'] ?? [];
        $statusMap = [
            'payment_intent.succeeded' => 'paid',
            'payment_intent.payment_failed' => 'failed',
            'charge.refunded' => 'refunded',
        ];
        return [
            'reference_id' => $object['id'] ?? null,
            'status' => $statusMap[$type] ?? 'pending',
            'amount' => isset($object['amount']) ? $object['amount'] / 100 : null,
            'currency' => strtoupper($object['currency'] ?? 'USD'),
            'raw' => $data,
        ];
    }

    public function getStatus(string $referenceId): array
    {
        $resp = $this->httpClient()->get($this->baseUrl . $this->intentPath . '/' . $referenceId);
        $data = $resp->json();
        return ['reference_id' => $referenceId, 'status' => $data['status'] ?? 'unknown', 'raw' => $data];
    }

    public function refund(string $referenceId, ?float $amount = null): array
    {
        $resp = $this->httpClient()->post($this->baseUrl . '/v1/refunds', [
            'payment_intent' => $referenceId,
            'amount' => $amount ? (int) round($amount * 100) : null,
        ]);
        return ['ok' => $resp->ok(), 'raw' => $resp->json()];
    }

    public function supportedMethods(): array
    {
        return ['credit_card', 'apple_pay', 'google_pay', 'sepa_debit', 'ideal'];
    }

    private function httpClient()
    {
        return Http::timeout(45)->withBasicAuth($this->apiKey, '');
    }
}
