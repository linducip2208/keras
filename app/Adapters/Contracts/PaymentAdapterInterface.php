<?php

namespace App\Adapters\Contracts;

interface PaymentAdapterInterface extends AdapterInterface
{
    /**
     * Create a payment intent / invoice.
     * Returns payment_url (for redirect) or client_secret (for embed) + reference_id.
     *
     * @param array{
     *   amount: float,
     *   currency: string,
     *   reference: string,
     *   customer_email?: string,
     *   customer_name?: string,
     *   customer_phone?: string,
     *   description?: string,
     *   items?: array,
     *   success_url?: string,
     *   cancel_url?: string,
     *   webhook_url?: string,
     *   methods?: array
     * } $payload
     * @return array{
     *   reference_id: string,
     *   status: string,
     *   payment_url?: string,
     *   qr_string?: string,
     *   va_number?: string,
     *   expires_at?: string,
     *   raw?: array
     * }
     */
    public function createCharge(array $payload): array;

    /**
     * Verify webhook signature and parse payload.
     */
    public function parseWebhook(string $rawBody, array $headers): array;

    /**
     * Get charge status by reference.
     */
    public function getStatus(string $referenceId): array;

    /**
     * Refund (if supported by provider).
     */
    public function refund(string $referenceId, ?float $amount = null): array;

    /**
     * Returns list of payment methods this adapter supports.
     */
    public function supportedMethods(): array;
}
