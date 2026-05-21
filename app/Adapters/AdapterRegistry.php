<?php

namespace App\Adapters;

use App\Adapters\Contracts\AdapterInterface;
use App\Models\AdapterConfig;

/**
 * Resolves & instantiates adapters from DB config (per CLAUDE.md §2 dynamic provider).
 */
class AdapterRegistry
{
    public const REGISTRY = [
        'llm' => [
            'openai_compatible' => \App\Adapters\LLM\OpenAICompatibleAdapter::class,
            // Anthropic, Gemini, Azure tinggal tambah class baru di sini
        ],
        'whatsapp' => [
            'generic_rest' => \App\Adapters\WhatsApp\GenericRestAdapter::class,
            'fonnte' => \App\Adapters\WhatsApp\GenericRestAdapter::class,
            'wati' => \App\Adapters\WhatsApp\GenericRestAdapter::class,
            'maytapi' => \App\Adapters\WhatsApp\GenericRestAdapter::class,
        ],
        'bank' => [
            'batch_file_bca' => \App\Adapters\Bank\BCABatchFileAdapter::class,
            'batch_file_mandiri' => \App\Adapters\Bank\MandiriBatchFileAdapter::class,
            // BNI/BRI/CIMB/Permata/Danamon → tambah class baru sesuai spec batch file
            // Instant: Flip/Xendit/Doku/OY → tambah class adapter REST API
        ],
    ];

    public function resolve(int $configId): AdapterInterface
    {
        $config = AdapterConfig::findOrFail($configId);
        $class = self::REGISTRY[$config->domain][$config->format] ?? null;
        abort_if(!$class, 422, "Adapter not registered: {$config->domain}/{$config->format}");

        /** @var AdapterInterface $adapter */
        $adapter = app($class);
        $adapter->configure([
            'base_url' => $config->base_url,
            'api_key' => $config->api_key_encrypted,
            'api_secret' => $config->api_secret_encrypted,
            'extra_headers' => $config->extra_headers ?? [],
            ...(array) ($config->extra_config ?? []),
        ]);

        return $adapter;
    }

    public function resolveDefaultForDomain(int $tenantId, string $domain): ?AdapterInterface
    {
        $config = AdapterConfig::where('tenant_id', $tenantId)
            ->where('domain', $domain)
            ->where('is_active', true)
            ->orderBy('id')
            ->first();

        return $config ? $this->resolve($config->id) : null;
    }
}
