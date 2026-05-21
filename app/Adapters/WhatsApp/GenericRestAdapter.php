<?php

namespace App\Adapters\WhatsApp;

use App\Adapters\Contracts\WhatsAppAdapterInterface;
use Illuminate\Support\Facades\Http;

/**
 * Generic REST WhatsApp adapter.
 * Compatible with: Fonnte, Wati, Maytapi, Cloud360, custom WA-Web API gateways.
 * Owner configures: base_url, api_key, headers per vendor.
 *
 * Untuk Meta Official: gunakan endpoint /messages dengan body Cloud API format.
 * Per CLAUDE.md §2: format-based, owner BYOK.
 */
class GenericRestAdapter implements WhatsAppAdapterInterface
{
    private string $baseUrl;
    private string $apiKey;
    private array $extraHeaders = [];
    private string $sendPath = '/send';

    public function configure(array $config): void
    {
        $this->baseUrl = rtrim($config['base_url'] ?? '', '/');
        $this->apiKey = $config['api_key'] ?? '';
        $this->extraHeaders = $config['extra_headers'] ?? [];
        $this->sendPath = $config['send_path'] ?? '/send';
    }

    public function test(): array
    {
        try {
            $resp = $this->httpClient()->get($this->baseUrl);
            return ['ok' => $resp->successful(), 'status' => $resp->status()];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    public function sendText(string $to, string $body): array
    {
        $resp = $this->httpClient()->post($this->baseUrl . $this->sendPath, [
            'target' => $to,
            'message' => $body,
        ]);
        return ['ok' => $resp->successful(), 'status' => $resp->status(), 'body' => $resp->json()];
    }

    public function sendMedia(string $to, string $mediaUrl, string $type, ?string $caption = null): array
    {
        $resp = $this->httpClient()->post($this->baseUrl . $this->sendPath, [
            'target' => $to,
            'url' => $mediaUrl,
            'type' => $type,
            'caption' => $caption,
        ]);
        return ['ok' => $resp->successful(), 'body' => $resp->json()];
    }

    public function sendTemplate(string $to, string $templateName, array $variables = []): array
    {
        $body = $templateName;
        foreach ($variables as $k => $v) {
            $body = str_replace('{' . $k . '}', $v, $body);
        }
        return $this->sendText($to, $body);
    }

    private function httpClient()
    {
        $headers = array_merge(
            $this->apiKey ? ['Authorization' => $this->apiKey] : [],
            $this->extraHeaders,
        );
        return Http::withHeaders($headers)->timeout(30)->acceptJson();
    }
}
