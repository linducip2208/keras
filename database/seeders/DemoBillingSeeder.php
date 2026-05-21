<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoBillingSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = DB::table('tenants')->where('slug', 'acme')->value('id');
        $basicPlan = DB::table('plans')->where('slug', 'basic')->first();
        if (!$tenantId || !$basicPlan) return;

        $now = now();
        $seats = 100;
        $monthly = $basicPlan->price_monthly * $seats; // 15k × 100 = 1.5jt
        $tax = $monthly * 0.11;
        $total = $monthly + $tax;

        // Subscription
        $subId = DB::table('subscriptions')->insertGetId([
            'tenant_id' => $tenantId,
            'plan_id' => $basicPlan->id,
            'status' => 'active',
            'current_period_start' => $now->copy()->startOfMonth(),
            'current_period_end' => $now->copy()->endOfMonth(),
            'billing_cycle' => 'monthly',
            'seats' => $seats,
            'auto_renew' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3 invoices: 1 paid (lalu), 1 open (sekarang), 1 past_due
        $invoices = [
            ['paid', -2, 'BCA Virtual Account'],
            ['open', 0, null],
            ['past_due', -1, null],
        ];

        $items = [
            ['description' => "Plan {$basicPlan->name} (monthly × {$seats} seats)", 'quantity' => $seats, 'unit_price' => $basicPlan->price_monthly, 'amount' => $monthly],
            ['description' => 'PPN 11%', 'amount' => $tax],
        ];

        foreach ($invoices as $i => [$status, $monthOffset, $paidVia]) {
            $start = $now->copy()->startOfMonth()->addMonths($monthOffset);
            $end = $now->copy()->endOfMonth()->addMonths($monthOffset);
            $issueDate = $start;
            $dueDate = $issueDate->copy()->addDays(7);

            DB::table('invoices')->insert([
                'tenant_id' => $tenantId,
                'subscription_id' => $subId,
                'number' => 'INV-' . $issueDate->format('Ym') . '-' . Str::upper(Str::random(6)),
                'period_start' => $start,
                'period_end' => $end,
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
                'subtotal' => $monthly,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'currency' => 'IDR',
                'status' => $status,
                'paid_at' => $status === 'paid' ? $issueDate->copy()->addDays(2) : null,
                'paid_via' => $paidVia,
                'line_items' => json_encode($items),
                'created_at' => $issueDate,
                'updated_at' => $now,
            ]);
        }

        // Demo Payment Adapter Configs (tenant-side BYOK)
        $adapters = [
            ['payment', 'Midtrans Snap (Production)', 'redirect_midtrans_snap', 'https://app.midtrans.com/snap/v1', 'SB-Mid-server-XXX'],
            ['payment', 'Xendit Invoice', 'redirect_xendit_invoice', 'https://api.xendit.co', 'xnd_production_XXX'],
            ['payment', 'QRIS Dynamic (Xendit)', 'qris_xendit', 'https://api.xendit.co', 'xnd_production_XXX'],
            ['payment', 'BCA Virtual Account', 'va_bca', 'https://api.xendit.co', 'xnd_production_XXX'],
            ['payment', 'DANA', 'ewallet_dana', 'https://api.xendit.co', 'xnd_production_XXX'],
            ['payment', 'GoPay', 'ewallet_gopay', 'https://api.midtrans.com', 'SB-Mid-server-XXX'],
            ['payment', 'Stripe Checkout (International)', 'redirect_stripe_checkout', 'https://api.stripe.com', 'sk_live_XXX'],
            ['payment', 'PayPal (International)', 'paypal', 'https://api-m.paypal.com', 'CLIENT_ID_XXX'],
            ['payment', 'Transfer Manual (BCA/Mandiri)', 'manual_bank_transfer', null, null],
            ['llm', 'OpenAI Production', 'openai_compatible', 'https://api.openai.com/v1', 'sk-DEMO_XXX'],
            ['llm', 'DeepSeek (Cheap)', 'openai_compatible', 'https://api.deepseek.com/v1', 'sk-DEMO_XXX'],
            ['llm', 'Groq Fast (Free Tier)', 'openai_compatible', 'https://api.groq.com/openai/v1', 'gsk_DEMO_XXX'],
        ];

        foreach ($adapters as [$domain, $name, $format, $url, $key]) {
            DB::table('adapter_configs')->updateOrInsert(
                ['tenant_id' => $tenantId, 'name' => $name],
                [
                    'domain' => $domain,
                    'format' => $format,
                    'base_url' => $url,
                    'api_key_encrypted' => $key ? encrypt($key) : null,
                    'is_active' => true,
                    'is_test_mode' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $this->command->info('Demo: 1 subscription + 3 invoices (paid/open/past_due) + 9 payment adapters + 3 LLM adapters.');
    }
}
