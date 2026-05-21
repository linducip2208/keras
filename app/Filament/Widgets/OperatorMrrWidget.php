<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Operator-only widget — only super_admin (HRIS Indonesia team) bisa lihat.
 * Show MRR/ARR + churn + tenant count.
 */
class OperatorMrrWidget extends BaseWidget
{
    protected ?string $heading = '💼 Operator Dashboard (Super Admin)';

    public static function canView(): bool
    {
        return (bool) auth()->user()?->is_super_admin;
    }

    protected function getStats(): array
    {
        // MRR — sum of monthly amount untuk all active subscriptions
        $activeSubs = Subscription::with('plan')
            ->whereIn('status', ['active', 'trial'])
            ->get();

        $mrr = $activeSubs->sum(function ($s) {
            $monthly = $s->billing_cycle === 'annual'
                ? ($s->plan->price_annual ?? 0) / 12
                : ($s->plan->price_monthly ?? 0);
            return $monthly * $s->seats;
        });

        $arr = $mrr * 12;

        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $trialTenants = Tenant::where('status', 'trial')->count();

        $paidInvoicesMonth = (float) Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total_amount');

        $churnedThisMonth = Subscription::whereBetween('canceled_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $newThisMonth = Subscription::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        return [
            Stat::make('MRR (Monthly Recurring Revenue)', 'Rp ' . number_format($mrr, 0, ',', '.'))
                ->description('ARR: Rp ' . number_format($arr, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Tenants', "{$activeTenants} aktif")
                ->description("Total: {$totalTenants} · Trial: {$trialTenants}")
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),

            Stat::make('Revenue Bulan Ini', 'Rp ' . number_format($paidInvoicesMonth, 0, ',', '.'))
                ->description('Total paid invoices ' . now()->format('M Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Subscription Movement', "+{$newThisMonth} / -{$churnedThisMonth}")
                ->description('New vs Canceled bulan ini')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($newThisMonth >= $churnedThisMonth ? 'success' : 'danger'),
        ];
    }
}
