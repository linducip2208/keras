<?php

namespace App\Adapters\Payment;

use App\Adapters\Contracts\PaymentAdapterInterface;
use Illuminate\Support\Facades\Http;

/**
 * Generic redirect-based checkout adapter.
 *
 * Compatible with payment gateways that return a hosted checkout URL:
 * - Midtrans Snap          (Indonesia)
 * - Xendit Invoice         (Indonesia + SEA)
 * - Doku Checkout          (Indonesia)
 * - Flip for Business      (Indonesia)
 * - OY Indonesia Checkout  (Indonesia)
 * - Stripe Checkout        (International)
 * - Paddle Checkout        (International)
 * - LemonSqueezy           (International)
 *
 * Owner configures: base_url, api_key, signature_key, webhook_secret.
 * Per CLAUDE.md §2 — format-based, BYOK. Tidak ada hardcoded vendor.
 */
class RedirectCheckoutAdapter implements PaymentAdapterInterface
{
    private string $baseUrl;
    private string $apiKey;
    private string $serverKey = '';
    private string $signatureKey = '';
    private string $createPath = '/v1/invoices';
    private string $statusPath = '/v1/invoices/{id}';
    private string $authStyle = 'bearer'; // bearer | basic
    private array $defaultPayload = [];

    public function configure(array $config): void
    {
        $this->baseUrl = rtrim($config['base_url'] ?? '', '/');
        $this->apiKey = $config['api_key'] ?? '';
        $this->serverKey = $config['server_key'] ?? '';
        $this->signatureKey = $config['signature_key'] ?? $config['webhook_secret'] ?? '';
        $this->createPath = $config['create_path'] ?? '/v1/invoices';
        $this->statusPath = $config['status_path'] ?? '/v1/invoices/{id}';
        $this->authStyle = $config['auth_style'] ?? 'bearer';
        $this->defaultPayload = $config['default_payload'] ?? [];
    }

    public function test(): array
    {
        $resp = $this->httpClient()->get($this->baseUrl . '/');
        return ['ok' => $resp->status() < 500, 'status' => $resp->status()];
    }

    public function createCharge(array $payload): array
    {
        $body = array_merge($this->defaultPayload, [
            'external_id' => $payload['reference'],
            'amount' => $payload['amount'],
            'currency' => $payload['currency'] ?? 'IDR',
            'payer_email' => $payload['customer_email'] ?? null,
            'description' => $payload['description'] ?? null,
            'customer' => [
                'given_names' => $payload['customer_name'] ?? null,
                'email' => $payload['customer_email'] ?? null,
                'mobile_number' => $payload['customer_phone'] ?? null,
            ],
            'success_redirect_url' => $payload['success_url'] ?? null,
            'failure_redirect_url' => $payload['cancel_url'] ?? null,
            'payment_methods' => $payload['methods'] ?? null,
        ]);

        $resp = $this->httpClient()->post($this->baseUrl . $this->createPath, $body);

        if (!$resp->ok()) {
            throw new \RuntimeException("Charge creation failed: " . $resp->body());
        }

        $data = $resp->json();
        return [
            'reference_id' => $data['id'] ?? ($data['external_id'] ?? $payload['reference']),
            'status' => $data['status'] ?? 'pending',
            'payment_url' => $data['invoice_url'] ?? $data['redirect_url'] ?? $data['url'] ?? null,
            'expires_at' => $data['expiry_date'] ?? null,
            'raw' => $data,
        ];
    }

    public function parseWebhook(string $rawBody, array $headers): array
    {
        // Verify signature (HMAC-SHA256 common pattern)
        $providedSignature = $headers['x-callback-token'][0]
            ?? $headers['x-signature'][0]
            ?? $headers['signature'][0]
            ?? '';

        if ($this->signatureKey && $providedSignature !== $this->signatureKey) {
            $expected = hash_hmac('sha256', $rawBody, $this->signatureKey);
            if (!hash_equals($expected, $providedSignature)) {
                throw new \RuntimeException('Invalid webhook signature');
            }
        }

        $data = json_decode($rawBody, true) ?: [];
        return [
            'reference_id' => $data['id'] ?? ($data['external_id'] ?? null),
            'status' => $this->normalizeStatus($data['status'] ?? 'unknown'),
            'amount' => $data['amount'] ?? null,
            'currency' => $data['currency'] ?? 'IDR',
            'paid_at' => $data['paid_at'] ?? $data['paid_time'] ?? null,
            'raw' => $data,
        ];
    }

    public function getStatus(string $referenceId): array
    {
        $url = $this->baseUrl . str_replace('{id}', $referenceId, $this->statusPath);
        $resp = $this->httpClient()->get($url);
        $data = $resp->json();
        return [
            'reference_id' => $referenceId,
            'status' => $this->normalizeStatus($data['status'] ?? 'unknown'),
            'raw' => $data,
        ];
    }

    public function refund(string $referenceId, ?float $amount = null): array
    {
        $resp = $this->httpClient()->post($this->baseUrl . "/v1/refunds", [
            'invoice_id' => $referenceId,
            'amount' => $amount,
        ]);
        return ['ok' => $resp->ok(), 'raw' => $resp->json()];
    }

    public function supportedMethods(): array
    {
        return ['credit_card', 'va', 'qris', 'ewallet', 'retail_outlet', 'bank_transfer'];
    }

    private function httpClient()
    {
        $client = Http::timeout(45)->acceptJson()->asJson();
        if ($this->authStyle === 'basic') {
            return $client->withBasicAuth($this->serverKey ?: $this->apiKey, '');
        }
        return $client->withToken($this->apiKey);
    }

    private function normalizeStatus(string $raw): string
    {
        $s = strtolower($raw);
        return match (true) {
            in_array($s, ['paid', 'settlement', 'completed', 'capture', 'success']) => 'paid',
            in_array($s, ['pending', 'authorize', 'processing', 'awaiting_payment']) => 'pending',
            in_array($s, ['expired', 'expire']) => 'expired',
            in_array($s, ['failed', 'failure', 'deny', 'cancel', 'canceled']) => 'failed',
            in_array($s, ['refunded', 'refund']) => 'refunded',
            default => $s,
        };
    }
}
