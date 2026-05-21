<?php

namespace App\Filament\Resources\AdapterConfigs\Schemas;

use App\Adapters\AdapterRegistry;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                        ->columnSpan(1),
                    TextInput::make('name')->label('Nama Konfigurasi')
                        ->placeholder('e.g., OpenAI Production, BCA KlikBCA Bisnis, Midtrans Snap')
                        ->required()->columnSpan(1),
                    Select::make('format')->label('Format Adapter')
                        ->options(fn ($get) => static::formatOptionsForDomain($get('domain')))
                        ->required()
                        ->searchable()
                        ->placeholder('Pilih domain dulu...')
                        ->columnSpanFull(),
                    TextInput::make('base_url')->label('Base URL')
                        ->url()->placeholder('https://api.provider.com/v1')
                        ->helperText('Endpoint dasar API. Untuk OpenAI: https://api.openai.com/v1. Untuk DeepSeek: https://api.deepseek.com/v1. Untuk Ollama lokal: http://localhost:11434/v1.')
                        ->columnSpanFull(),
                ]),

            Section::make('Credentials (Encrypted)')
                ->description('API key & secret di-encrypt at rest (AES-256). Tidak pernah di-log atau di-expose di API response.')
                ->columns(2)
                ->schema([
                    TextInput::make('api_key_encrypted')->label('API Key')
                        ->password()->revealable()
                        ->placeholder('sk-... atau token apapun dari provider')
                        ->columnSpan(1),
                    TextInput::make('api_secret_encrypted')->label('API Secret (opsional)')
                        ->password()->revealable()
                        ->placeholder('Hanya jika provider butuh client_secret (PayPal, dll.)')
                        ->columnSpan(1),
                    KeyValue::make('extra_headers')->label('Extra Headers (opsional)')
                        ->keyLabel('Header Name')
                        ->valueLabel('Header Value')
                        ->reorderable()
                        ->columnSpanFull(),
                    KeyValue::make('extra_config')->label('Extra Config (opsional)')
                        ->keyLabel('Key')
                        ->valueLabel('Value')
                        ->helperText('Misal: webhook_secret, verify_token, default_bank, merchant_id, default_channel')
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
            'openai_compatible' => 'OpenAI-Compatible (OpenAI, DeepSeek, Groq, Mistral, Together, OpenRouter, Ollama, LM Studio, vLLM)',
            'generic_rest' => 'Generic REST (custom)',
            'fonnte' => 'Fonnte',
            'wati' => 'Wati',
            'maytapi' => 'Maytapi',
            'batch_file_bca' => 'BCA KlikBCA Bisnis (Batch CSV)',
            'batch_file_mandiri' => 'Mandiri Cash Management (Batch TXT)',
            'redirect_checkout' => 'Redirect Checkout (Hosted Page)',
            'redirect_midtrans_snap' => 'Midtrans Snap',
            'redirect_xendit_invoice' => 'Xendit Invoice',
            'redirect_doku_checkout' => 'Doku Checkout',
            'redirect_flip_business' => 'Flip For Business',
            'redirect_oy_indonesia' => 'OY! Indonesia',
            'redirect_stripe_checkout' => 'Stripe Checkout (International)',
            'redirect_paddle' => 'Paddle (International)',
            'redirect_lemonsqueezy' => 'LemonSqueezy (International)',
            'embed_checkout' => 'Embed Checkout (JS SDK)',
            'embed_stripe_elements' => 'Stripe Elements',
            'embed_midtrans_snap_embed' => 'Midtrans Snap Embed',
            'qris' => 'QRIS Dynamic (semua e-wallet ID)',
            'qris_midtrans' => 'Midtrans QRIS',
            'qris_xendit' => 'Xendit QRIS',
            'va' => 'Virtual Account (multi-bank)',
            'va_midtrans' => 'Midtrans VA',
            'va_xendit' => 'Xendit VA',
            'va_flip' => 'Flip VA',
            'ewallet' => 'E-Wallet (DANA/OVO/GoPay/ShopeePay/dll.)',
            'ewallet_dana' => 'DANA',
            'ewallet_ovo' => 'OVO',
            'ewallet_gopay' => 'GoPay',
            'ewallet_shopeepay' => 'ShopeePay',
            'paypal' => 'PayPal (International)',
            'manual_bank_transfer' => 'Manual Bank Transfer (admin confirm)',
        ];

        foreach (array_keys($formats) as $slug) {
            $labels[$slug] = $friendlyLabels[$slug] ?? $slug;
        }

        return $labels;
    }
}
