<?php

namespace App\Adapters\Payment;

use App\Adapters\Contracts\PaymentAdapterInterface;
use Illuminate\Support\Facades\Http;

/**
 * PayPal REST API adapter — International.
 *
 * Uses OAuth2 client_credentials flow + Orders API v2.
 * Compatible: PayPal Business, PayPal Checkout, PayPal Subscriptions.
 */
class PayPalAdapter implements PaymentAdapterInterface
{
    private string $baseUrl = 'https://api-m.paypal.com';
    private string $clientId;
    private string $clientSecret;
    private ?string $accessToken = null;

    public function configure(array $config): void
    {
        $this->baseUrl = rtrim($config['base_url'] ?? 'https://api-m.paypal.com', '/');
        $this->clientId = $config['api_key'] ?? '';
        $this->clientSecret = $config['api_secret'] ?? '';
    }

    public function test(): array
    {
        try {
            $this->ensureToken();
            return ['ok' => true, 'message' => 'PayPal token OK'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    public function createCharge(array $payload): array
    {
        $this->ensureToken();

        $resp = Http::withToken($this->accessToken)
            ->withHeaders(['PayPal-Request-Id' => $payload['reference']])
            ->post($this->baseUrl . '/v2/checkout/orders', [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $payload['reference'],
                    'description' => $payload['description'] ?? 'Subscription',
                    'amount' => [
                        'currency_code' => strtoupper($payload['currency'] ?? 'USD'),
                        'value' => number_format($payload['amount'], 2, '.', ''),
                    ],
                ]],
                'application_context' => [
                    'return_url' => $payload['success_url'] ?? null,
                    'cancel_url' => $payload['cancel_url'] ?? null,
                ],
            ]);

        if (!$resp->ok()) {
            throw new \RuntimeException('PayPal create order failed: ' . $resp->body());
        }

        $data = $resp->json();
        $approveLink = collect($data['links'] ?? [])->firstWhere('rel', 'approve');
        return [
            'reference_id' => $data['id'],
            'status' => 'pending',
            'payment_url' => $approveLink['href'] ?? null,
            'raw' => $data,
        ];
    }

    public function parseWebhook(string $rawBody, array $headers): array
    {
        $data = json_decode($rawBody, true) ?: [];
        $type = $data['event_type'] ?? '';
        $statusMap = [
            'PAYMENT.CAPTURE.COMPLETED' => 'paid',
            'CHECKOUT.ORDER.APPROVED' => 'pending',
            'PAYMENT.CAPTURE.DENIED' => 'failed',
            'PAYMENT.CAPTURE.REFUNDED' => 'refunded',
        ];
        $resource = $data['resource'] ?? [];
        return [
            'reference_id' => $resource['supplementary_data']['related_ids']['order_id']
                ?? $resource['id']
                ?? null,
            'status' => $statusMap[$type] ?? 'pending',
            'amount' => $resource['amount']['value'] ?? null,
            'currency' => $resource['amount']['currency_code'] ?? 'USD',
            'raw' => $data,
        ];
    }

    public function getStatus(string $referenceId): array
    {
        $this->ensureToken();
        $resp = Http::withToken($this->accessToken)->get($this->baseUrl . "/v2/checkout/orders/{$referenceId}");
        return ['reference_id' => $referenceId, 'status' => $resp->json('status', 'unknown'), 'raw' => $resp->json()];
    }

    public function refund(string $referenceId, ?float $amount = null): array
    {
        $this->ensureToken();
        $resp = Http::withToken($this->accessToken)->post($this->baseUrl . "/v2/payments/captures/{$referenceId}/refund", [
            'amount' => $amount ? ['value' => number_format($amount, 2, '.', ''), 'currency_code' => 'USD'] : null,
        ]);
        return ['ok' => $resp->ok(), 'raw' => $resp->json()];
    }

    public function supportedMethods(): array
    {
        return ['paypal', 'paypal_credit', 'paylater', 'venmo'];
    }

    private function ensureToken(): void
    {
        if ($this->accessToken) return;
        $resp = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post($this->baseUrl . '/v1/oauth2/token', ['grant_type' => 'client_credentials']);
        $this->accessToken = $resp->json('access_token');
        if (!$this->accessToken) {
            throw new \RuntimeException('PayPal token fetch failed: ' . $resp->body());
        }
    }
}
