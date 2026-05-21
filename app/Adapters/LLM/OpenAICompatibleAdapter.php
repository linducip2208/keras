<?php

namespace App\Adapters\LLM;

use App\Adapters\Contracts\LLMAdapterInterface;
use Illuminate\Support\Facades\Http;

/**
 * Generic OpenAI-compatible LLM adapter.
 * Compatible with: OpenAI, DeepSeek, Groq, Mistral, Together, Fireworks,
 * OpenRouter, xAI, Cerebras, Anyscale, Ollama, LM Studio, vLLM, LiteLLM, dst.
 *
 * Per CLAUDE.md §2: format-based (bukan per-vendor), owner BYOK.
 */
class OpenAICompatibleAdapter implements LLMAdapterInterface
{
    private string $baseUrl;
    private string $apiKey;
    private array $extraHeaders = [];

    public function configure(array $config): void
    {
        $this->baseUrl = rtrim($config['base_url'] ?? 'https://api.openai.com/v1', '/');
        $this->apiKey = $config['api_key'] ?? '';
        $this->extraHeaders = $config['extra_headers'] ?? [];
    }

    public function test(): array
    {
        try {
            $resp = $this->httpClient()->get($this->baseUrl . '/models');
            return ['ok' => $resp->ok(), 'status' => $resp->status(), 'message' => $resp->ok() ? 'OK' : $resp->body()];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    public function complete(array $messages, ?string $model = null, array $options = []): array
    {
        $payload = array_merge([
            'model' => $model ?? 'gpt-4o-mini',
            'messages' => $messages,
            'temperature' => 0.7,
        ], $options);

        $resp = $this->httpClient()->post($this->baseUrl . '/chat/completions', $payload);

        if (!$resp->ok()) {
            throw new \RuntimeException('LLM call failed: ' . $resp->body());
        }

        $data = $resp->json();
        return [
            'content' => $data['choices'][0]['message']['content'] ?? '',
            'usage' => $data['usage'] ?? [],
            'model' => $data['model'] ?? $payload['model'],
        ];
    }

    public function listModels(): array
    {
        $resp = $this->httpClient()->get($this->baseUrl . '/models');
        if (!$resp->ok()) return [];
        $data = $resp->json();
        return collect($data['data'] ?? [])->pluck('id')->all();
    }

    private function httpClient()
    {
        return Http::withToken($this->apiKey)
            ->withHeaders($this->extraHeaders)
            ->timeout(60)
            ->acceptJson();
    }
}
