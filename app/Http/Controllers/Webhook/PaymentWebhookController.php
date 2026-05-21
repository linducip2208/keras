<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\AdapterConfig;
use App\Services\Billing\BillingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentWebhookController extends Controller
{
    public function __construct(private BillingService $billing) {}

    public function handle(Request $request, int $adapterConfigId)
    {
        $adapterConfig = AdapterConfig::find($adapterConfigId);
        if (!$adapterConfig || $adapterConfig->domain !== 'payment') {
            return response()->json(['error' => 'Invalid adapter'], 404);
        }

        $rawBody = $request->getContent();
        $headers = collect($request->headers->all())
            ->mapWithKeys(fn ($v, $k) => [strtolower($k) => $v])
            ->all();

        // Log webhook for audit (always, before processing)
        DB::table('payment_webhook_logs')->insert([
            'tenant_id' => $adapterConfig->tenant_id,
            'adapter_slug' => $adapterConfig->format,
            'event_type' => $request->input('event_type') ?? $request->input('type'),
            'headers' => json_encode($headers),
            'raw_payload' => $rawBody,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $result = $this->billing->processWebhook($adapterConfig, $rawBody, $headers);
            return response()->json(['received' => true, 'result' => $result]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
