<?php

namespace App\Filament\Resources\AdapterConfigs\Schemas;

use App\Adapters\AdapterRegistry;
use App\Services\Integrations\PaymentPresetRegistry;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class AdapterConfigForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Provider Integrasi')
                ->description('Konfigurasikan provider eksternal. Owner BYOK — API key di-encrypt at rest.')
                ->columns(2)
                ->schema([
                    Select::make('domain')->label('Domain Integrasi')
                        ->options([
                            'llm' => '🧠 LLM / AI',
                            'whatsapp' => '💬 WhatsApp Gateway',
                            'bank' => '🏦 Bank Disbursement',
                            'payment' => '💳 Payment Gateway',
                            'esign' => '✍️ E-Signature',
                            'ocr' => '🔍 OCR / Vision',
                            'mail' => '📧 Email',
                            'sms' => '📱 SMS Gateway',
                            'storage' => '📦 File Storage',
                            'accounting' => '📊 Accounting',
                        ])
                        ->required()
                        ->reactive()
                        ->live()
                        ->columnSpan(1),
                    TextInput::make('name')->label('Nama Konfigurasi')
                        ->placeholder('e.g., OpenAI Production, BCA KlikBCA Bisnis, Midtrans Snap')
                        ->required()->columnSpan(1),

                    // Preset picker — hanya muncul kalau domain='payment'
                    Select::make('preset_slug')->label('🎁 Preset Payment Gateway (auto-fill)')
                        ->visible(fn ($get) => $get('domain') === 'payment')
                        ->options(fn () => app(PaymentPresetRegistry::class)->groupedOptions())
                        ->searchable()
                        ->placeholder('Pilih dari daftar (Indonesia / SEA / International)...')
                        ->helperText('Pilih provider, lalu format + base URL auto-isi. Anda tetap input API key sendiri (BYOK).')
                        ->reactive()
                        ->live()
                        ->afterStateUpdated(function ($state, $set) {
                            if (!$state) return;
                            $preset = app(PaymentPresetRegistry::class)->findBySlug($state);
                            if (!$preset) return;
                            $set('format', $preset['format'] ?? null);
                            $set('base_url', $preset['base_url_production'] ?? null);
                            if (empty($set->name ?? null)) {
                                $set('name', $preset['name']);
                            }
                        })
                        ->columnSpanFull(),

                    Placeholder::make('preset_info')
                        ->label('')
                        ->visible(fn ($get) => $get('domain') === 'payment' && $get('preset_slug'))
                        ->content(function ($get) {
                            $preset = app(PaymentPresetRegistry::class)->findBySlug($get('preset_slug'));
                            if (!$preset) return '';
                            $methods = implode(', ', $preset['methods'] ?? []);
                            $docs = !empty($preset['docs_url']) ? '<a href="' . $preset['docs_url'] . '" target="_blank" class="text-blue-600 underline">📚 Docs</a>' : '';
                            $note = !empty($preset['note']) ? "<div class='mt-2 text-amber-700 bg-amber-50 p-2 rounded'>ℹ️ {$preset['note']}</div>" : '';
                            return new HtmlString("
                                <div class='bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm'>
                                    <div><strong>Vendor:</strong> {$preset['vendor']}</div>
                                    <div><strong>Methods:</strong> <code class='text-xs'>{$methods}</code></div>
                                    <div class='mt-1'>{$docs}</div>
                                    {$note}
                                </div>
                            ");
                        })
                        ->columnSpanFull(),

                    Select::make('format')->label('Format Adapter')
                        ->options(fn ($get) => static::formatOptionsForDomain($get('domain')))
                        ->required()
                        ->searchable()
                        ->placeholder('Pilih domain dulu...')
                        ->columnSpanFull(),
                    TextInput::make('base_url')->label('Base URL')
                        ->url()->placeholder('https://api.provider.com/v1')
                        ->helperText('Endpoint dasar API.')
                        ->columnSpanFull(),
                ]),

            Section::make('Credentials (Encrypted)')
                ->description('API key & secret di-encrypt at rest (AES-256). Tidak pernah di-log atau di-expose di API response.')
                ->columns(2)
                ->schema([
                    TextInput::make('api_key_encrypted')->label('API Key')
                        ->password()->revealable()
                        ->placeholder('sk-..., xnd_..., SB-Mid-server-..., atau token apapun dari provider')
                        ->columnSpan(1),
                    TextInput::make('api_secret_encrypted')->label('API Secret (opsional)')
                        ->password()->revealable()
                        ->placeholder('Hanya jika provider butuh client_secret (PayPal, Paddle, dll.)')
                        ->columnSpan(1),
                    KeyValue::make('extra_headers')->label('Extra Headers (opsional)')
                        ->keyLabel('Header Name')
                        ->valueLabel('Header Value')
                        ->reorderable()
                        ->columnSpanFull(),
                    KeyValue::make('extra_config')->label('Extra Config (opsional)')
                        ->keyLabel('Key')
                        ->valueLabel('Value')
                        ->helperText('Misal: webhook_secret, server_key, client_key, merchant_id, vendor_id, default_channel')
                        ->columnSpanFull(),
                ]),

            Section::make('Mode')
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')->label('Aktif')->default(true),
                    Toggle::make('is_test_mode')->label('Test/Sandbox Mode')
                        ->helperText('Pakai sandbox endpoint provider untuk testing'),
                ]),

            Select::make('tenant_id')->relationship('tenant', 'name')
                ->visible(fn () => auth()->user()?->is_super_admin)
                ->required(),
        ]);
    }

    private static function formatOptionsForDomain(?string $domain): array
    {
        if (!$domain) return [];

        $formats = AdapterRegistry::REGISTRY[$domain] ?? [];
        $labels = [];

        $friendlyLabels = [
            'openai_compatible' => 'OpenAI-Compatible (OpenAI/DeepSeek/Groq/Mistral/OpenRouter/Ollama/LM Studio/vLLM)',
            'generic_rest' => 'Generic REST (custom)',
            'fonnte' => 'Fonnte', 'wati' => 'Wati', 'maytapi' => 'Maytapi',
            'batch_file_bca' => 'BCA KlikBCA Bisnis (Batch CSV)',
            'batch_file_mandiri' => 'Mandiri Cash Management (Batch TXT)',
            'redirect_checkout' => 'Generic Redirect Checkout (Hosted Page)',
            'redirect_midtrans_snap' => 'Midtrans Snap (Indonesia)',
            'redirect_xendit_invoice' => 'Xendit Invoice (Indonesia/SEA)',
            'redirect_doku_checkout' => 'Doku Checkout (Indonesia)',
            'redirect_flip_business' => 'Flip For Business (Indonesia)',
            'redirect_oy_indonesia' => 'OY! Indonesia',
            'redirect_stripe_checkout' => 'Stripe Checkout (International)',
            'redirect_paddle' => 'Paddle (International)',
            'redirect_lemonsqueezy' => 'LemonSqueezy (International)',
            'embed_checkout' => 'Generic Embed Checkout',
            'embed_stripe_elements' => 'Stripe Elements (Embed)',
            'embed_midtrans_snap_embed' => 'Midtrans Snap Embed',
            'embed_adyen_dropin' => 'Adyen Drop-in',
            'qris' => 'QRIS Generic (semua e-wallet ID)',
            'qris_midtrans' => 'Midtrans QRIS', 'qris_xendit' => 'Xendit QRIS',
            'qris_doku' => 'Doku QRIS', 'qris_linkaja' => 'LinkAja QRIS',
            'va' => 'Generic Virtual Account', 'va_midtrans' => 'Midtrans VA',
            'va_xendit' => 'Xendit VA', 'va_doku' => 'Doku VA',
            'va_flip' => 'Flip VA', 'va_oy' => 'OY! VA',
            'ewallet' => 'Generic E-Wallet (DANA/OVO/GoPay/ShopeePay/SEA/Int.)',
            'ewallet_dana' => 'DANA', 'ewallet_ovo' => 'OVO',
            'ewallet_gopay' => 'GoPay', 'ewallet_shopeepay' => 'ShopeePay',
            'ewallet_linkaja' => 'LinkAja',
            'paypal' => 'PayPal (International)',
            'manual_bank_transfer' => 'Manual Bank Transfer (admin confirm)',
        ];

        foreach (array_keys($formats) as $slug) {
            $labels[$slug] = $friendlyLabels[$slug] ?? $slug;
        }

        return $labels;
    }
}
