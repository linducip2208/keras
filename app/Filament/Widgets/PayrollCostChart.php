<?php

namespace App\Filament\Widgets;

use App\Models\PayrollRun;
use Filament\Widgets\ChartWidget;

class PayrollCostChart extends ChartWidget
{
    protected ?string $heading = 'Tren Biaya Payroll 6 Bulan';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $runs = PayrollRun::with('period')
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->limit(6)
            ->get()
            ->sortBy('completed_at');

        return [
            'datasets' => [
                [
                    'label' => 'Gross',
                    'data' => $runs->pluck('total_gross')->map(fn ($v) => (float) $v)->values()->all(),
                    'borderColor' => '#f97316',
                    'backgroundColor' => 'rgba(249, 115, 22, 0.1)',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Net',
                    'data' => $runs->pluck('total_net')->map(fn ($v) => (float) $v)->values()->all(),
                    'borderColor' => '#16a34a',
                    'backgroundColor' => 'rgba(22, 163, 74, 0.1)',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'PPh 21 + BPJS',
                    'data' => $runs->map(fn ($r) => (float) ($r->total_tax + $r->total_bpjs_employer + $r->total_bpjs_employee))->values()->all(),
                    'borderColor' => '#dc2626',
                    'backgroundColor' => 'rgba(220, 38, 38, 0.1)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $runs->pluck('period.name')->values()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
