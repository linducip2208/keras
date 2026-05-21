<?php

namespace App\Adapters\Payment;

use App\Adapters\Contracts\PaymentAdapterInterface;
use Illuminate\Support\Facades\Http;

/**
 * QRIS Dynamic adapter — Indonesia only.
 *
 * Compatible with provider yang return QR string (TLV format) untuk render QR code:
 * - Midtrans Core API QRIS    (Indonesia)
 * - Xendit QRIS               (Indonesia)
 * - Doku QRIS                  (Indonesia)
 * - LinkAja QRIS              (Indonesia)
 * - Custom QRIS NMID acquiring (BCA, Mandiri, BNI, BRI as QRIS issuer)
 *
 * All Indonesian e-wallets (DANA, OVO, GoPay, ShopeePay, LinkAja, JakOne) bisa scan QRIS dynamic.
 */
class QRISAdapter implements PaymentAdapterInterface
{
    private string $baseUrl;
    private string $apiKey;
    private string $merchantId = '';
    private string $createPath = '/v2/qris';

    public function configure(array $config): void
    {
        $this->baseUrl = rtrim($config['base_url'] ?? '', '/');
        $this->apiKey = $config['api_key'] ?? '';
        $this->merchantId = $config['merchant_id'] ?? '';
        $this->createPath = $config['create_path'] ?? '/v2/qris';
    }

    public function test(): array
    {
        $resp = $this->httpClient()->get($this->baseUrl);
        return ['ok' => $resp->status() < 500];
    }

    public function createCharge(array $payload): array
    {
        $resp = $this->httpClient()->post($this->baseUrl . $this->createPath, [
            'reference_id' => $payload['reference'],
            'amount' => (int) $payload['amount'],
            'currency' => 'IDR',
            'channel_code' => 'QR_ID',
            'merchant_id' => $this->merchantId,
            'expires_at' => $payload['expires_at'] ?? now()->addMinutes(30)->toIso8601String(),
        ]);

        if (!$resp->ok()) {
            throw new \RuntimeException('QRIS creation failed: ' . $resp->body());
        }

        $data = $resp->json();
        return [
            'reference_id' => $data['id'] ?? $payload['reference'],
            'status' => 'pending',
            'qr_string' => $data['qr_string'] ?? null,
            'qr_image_url' => $data['qr_image'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'raw' => $data,
        ];
    }

    public function parseWebhook(string $rawBody, array $headers): array
    {
        $data = json_decode($rawBody, true) ?: [];
        $statusMap = ['SUCCEEDED' => 'paid', 'COMPLETED' => 'paid', 'FAILED' => 'failed', 'EXPIRED' => 'expired'];
        return [
            'reference_id' => $data['data']['reference_id'] ?? $data['reference_id'] ?? null,
            'status' => $statusMap[strtoupper($data['data']['status'] ?? '')] ?? 'pending',
            'amount' => $data['data']['amount'] ?? null,
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
        return ['ok' => false, 'message' => 'QRIS refund tidak didukung — gunakan kanal refund manual'];
    }

    public function supportedMethods(): array
    {
        return ['qris', 'dana', 'ovo', 'gopay', 'shopeepay', 'linkaja', 'jakone'];
    }

    private function httpClient()
    {
        return Http::timeout(30)->acceptJson()->asJson()->withBasicAuth($this->apiKey, '');
    }
}
