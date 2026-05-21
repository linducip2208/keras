<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\AdapterConfig;
use App\Services\WhatsApp\BotEngine;
use Illuminate\Http\Request;

class WhatsAppWebhookController extends Controller
{
    public function __construct(private BotEngine $bot) {}

    public function handle(Request $request, int $adapterConfigId)
    {
        $config = AdapterConfig::find($adapterConfigId);
        if (!$config || $config->domain !== 'whatsapp') {
            return response()->json(['error' => 'Invalid adapter'], 404);
        }

        // Parse common WA inbound shapes (Meta Cloud API, Wati, Fonnte, Maytapi)
        $message = $this->normalizeMessage($request->all(), $config->format);
        if (!$message) {
            return response()->json(['received' => true, 'note' => 'No actionable message']);
        }

        try {
            $this->bot->handleInbound($message, $config->tenant_id);
            return response()->json(['received' => true]);
        } catch (\Throwable $e) {
            logger()->error('WA webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function verify(Request $request, int $adapterConfigId)
    {
        // Meta Cloud API webhook verification (echo hub.challenge if mode=subscribe + valid token)
        if ($request->query('hub_mode') === 'subscribe') {
            $config = AdapterConfig::find($adapterConfigId);
            $expectedToken = $config?->extra_config['verify_token'] ?? '';
            if ($request->query('hub_verify_token') === $expectedToken) {
                return response($request->query('hub_challenge'), 200);
            }
            return response('Forbidden', 403);
        }
        return response('OK', 200);
    }

    private function normalizeMessage(array $payload, string $format): ?array
    {
        // Meta Cloud API format
        if (isset($payload['entry'][0]['changes'][0]['value']['messages'][0])) {
            $m = $payload['entry'][0]['changes'][0]['value']['messages'][0];
            return [
                'from' => $m['from'] ?? '',
                'type' => $m['type'] ?? 'text',
                'text' => $m['text']['body'] ?? '',
                'body' => $m['text']['body'] ?? '',
                'latitude' => $m['location']['latitude'] ?? null,
                'longitude' => $m['location']['longitude'] ?? null,
                'image' => $m['image'] ?? null,
            ];
        }

        // Fonnte / Wati / generic REST format
        if (isset($payload['sender']) || isset($payload['from'])) {
            return [
                'from' => $payload['sender'] ?? $payload['from'] ?? '',
                'type' => $payload['type'] ?? 'text',
                'text' => $payload['message'] ?? $payload['text'] ?? $payload['body'] ?? '',
                'body' => $payload['message'] ?? $payload['text'] ?? $payload['body'] ?? '',
                'latitude' => $payload['location']['lat'] ?? $payload['latitude'] ?? null,
                'longitude' => $payload['location']['lng'] ?? $payload['longitude'] ?? null,
            ];
        }

        return null;
    }
}
