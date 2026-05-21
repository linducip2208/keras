<?php

namespace App\Adapters\Contracts;

interface WhatsAppAdapterInterface extends AdapterInterface
{
    public function sendText(string $to, string $body): array;
    public function sendMedia(string $to, string $mediaUrl, string $type, ?string $caption = null): array;
    public function sendTemplate(string $to, string $templateName, array $variables = []): array;
}
