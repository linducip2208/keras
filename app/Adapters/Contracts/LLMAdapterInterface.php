<?php

namespace App\Adapters\Contracts;

interface LLMAdapterInterface extends AdapterInterface
{
    /**
     * Send a chat completion request.
     * @param array<int, array{role:string, content:string}> $messages
     * @return array{content:string, usage:array, model:string}
     */
    public function complete(array $messages, ?string $model = null, array $options = []): array;

    /**
     * List available models (untuk autocomplete owner).
     */
    public function listModels(): array;
}
