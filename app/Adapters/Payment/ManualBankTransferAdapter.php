<?php

namespace App\Adapters\Payment;

use App\Adapters\Contracts\PaymentAdapterInterface;

/**
 * Manual bank transfer — owner display rekening, customer transfer, admin confirm.
 * Tidak ada API call ke external. Untuk UKM yang belum punya payment gateway.
 */
class ManualBankTransferAdapter implements PaymentAdapterInterface
{
    private array $bankAccounts = [];
    private string $instructionsTemplate = '';

    public function configure(array $config): void
    {
        $this->bankAccounts = $config['bank_accounts'] ?? [];
        $this->instructionsTemplate = $config['instructions'] ?? 'Transfer ke rekening berikut lalu upload bukti transfer.';
    }

    public function test(): array
    {
        return ['ok' => count($this->bankAccounts) > 0, 'message' => 'Manual transfer config OK'];
    }

    public function createCharge(array $payload): array
    {
        return [
            'reference_id' => 'MAN-' . $payload['reference'],
            'status' => 'pending',
            'bank_accounts' => $this->bankAccounts,
            'instructions' => $this->instructionsTemplate,
            'amount' => $payload['amount'],
            'currency' => $payload['currency'] ?? 'IDR',
            'expires_at' => now()->addDays(3)->toIso8601String(),
            'raw' => ['manual' => true],
        ];
    }

    public function parseWebhook(string $rawBody, array $headers): array
    {
        return ['reference_id' => null, 'status' => 'pending', 'raw' => []];
    }

    public function getStatus(string $referenceId): array
    {
        return ['reference_id' => $referenceId, 'status' => 'manual_pending', 'raw' => []];
    }

    public function refund(string $referenceId, ?float $amount = null): array
    {
        return ['ok' => false, 'message' => 'Refund manual via bank reversal'];
    }

    public function supportedMethods(): array
    {
        return ['bank_transfer_manual'];
    }
}
