<?php

namespace App\Adapters\Payment;

use App\Adapters\Contracts\PaymentAdapterInterface;
use Illuminate\Support\Facades\Http;

/**
 * E-Wallet direct charge adapter (Indonesia + International).
 *
 * Compatible with:
 * - DANA, OVO, GoPay, ShopeePay, LinkAja      (Indonesia, via Xendit/Midtrans)
 * - GrabPay, AliPay, WeChat Pay                (SEA + China, via Xendit/Stripe)
 * - Apple Pay, Google Pay, Samsung Pay         (International, via Stripe)
 * - PayPal Direct                               (handled by separate PayPalAdapter)
 */
class EWalletAdapter implements PaymentAdapterInterface
{
    private string $baseUrl;
    private string $apiKey;
    private string $createPath = '/v2/ewallets/charges';
    private string $defaultChannel = 'ID_DANA';

    public function configure(array $config): void
    {
        $this->baseUrl = rtrim($config['base_url'] ?? '', '/');
        $this->apiKey = $config['api_key'] ?? '';
        $this->createPath = $config['create_path'] ?? '/v2/ewallets/charges';
        $this->defaultChannel = $config['default_channel'] ?? 'ID_DANA';
    }

    public function test(): array
    {
        return ['ok' => !empty($this->baseUrl)];
    }

    public function createCharge(array $payload): array
    {
        $channel = $payload['channel_code'] ?? $this->defaultChannel;
        $resp = $this->httpClient()->post($this->baseUrl . $this->createPath, [
            'reference_id' => $payload['reference'],
            'amount' => (int) $payload['amount'],
            'currency' => $payload['currency'] ?? 'IDR',
            'checkout_method' => 'ONE_TIME_PAYMENT',
            'channel_code' => $channel,
            'channel_properties' => [
                'success_redirect_url' => $payload['success_url'] ?? null,
                'failure_redirect_url' => $payload['cancel_url'] ?? null,
                'mobile_number' => $payload['customer_phone'] ?? null,
            ],
        ]);

        if (!$resp->ok()) {
            throw new \RuntimeException('E-wallet charge failed: ' . $resp->body());
        }

        $data = $resp->json();
        return [
            'reference_id' => $data['id'] ?? $payload['reference'],
            'status' => 'pending',
            'payment_url' => $data['actions']['mobile_web_checkout_url']
                ?? $data['actions']['desktop_web_checkout_url']
                ?? null,
            'deeplink' => $data['actions']['mobile_deeplink_checkout_url'] ?? null,
            'channel_code' => $channel,
            'raw' => $data,
        ];
    }

    public function parseWebhook(string $rawBody, array $headers): array
    {
        $data = json_decode($rawBody, true) ?: [];
        $statusMap = ['SUCCEEDED' => 'paid', 'FAILED' => 'failed', 'VOIDED' => 'failed'];
        return [
            'reference_id' => $data['data']['reference_id'] ?? null,
            'status' => $statusMap[strtoupper($data['data']['status'] ?? '')] ?? 'pending',
            'amount' => $data['data']['amount'] ?? null,
            'currency' => $data['data']['currency'] ?? 'IDR',
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
        $resp = $this->httpClient()->post($this->baseUrl . $this->createPath . '/' . $referenceId . '/refunds', [
            'amount' => $amount,
        ]);
        return ['ok' => $resp->ok(), 'raw' => $resp->json()];
    }

    public function supportedMethods(): array
    {
        return [
            'ID_DANA','ID_OVO','ID_LINKAJA','ID_SHOPEEPAY','ID_ASTRAPAY','ID_JENIUSPAY',
            'PH_GCASH','PH_GRABPAY','PH_PAYMAYA',
            'TH_TRUEMONEY','TH_LINEPAY','TH_RABBIT_LINEPAY',
            'MY_TOUCHNGO','MY_GRABPAY','MY_SHOPEEPAY',
            'VN_MOMO','VN_VNPAY','VN_ZALOPAY',
        ];
    }

    private function httpClient()
    {
        return Http::timeout(30)->acceptJson()->asJson()->withBasicAuth($this->apiKey, '');
    }
}
