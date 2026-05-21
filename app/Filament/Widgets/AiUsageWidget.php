<?php

namespace App\Filament\Widgets;

use App\Models\AIScreening;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class AiUsageWidget extends BaseWidget
{
    protected ?string $heading = '🧠 AI Usage Bulan Ini';

    protected function getStats(): array
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $totalRuns = AIScreening::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        $completed = AIScreening::whereBetween('created_at', [$monthStart, $monthEnd])->where('status', 'completed')->count();
        $avgScore = (float) AIScreening::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'completed')
            ->avg('score');

        $topProvider = AIScreening::whereBetween('created_at', [$monthStart, $monthEnd])
            ->select('adapter_name', DB::raw('COUNT(*) as count'), DB::raw('SUM(cost) as total_cost'))
            ->groupBy('adapter_name')
            ->orderByDesc('count')
            ->first();

        $strongCandidates = AIScreening::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 'completed')
            ->whereIn('recommendation', ['strong_yes', 'yes'])
            ->distinct('application_id')
            ->count('application_id');

        return [
            Stat::make('AI Screening Runs', $totalRuns)
                ->description("{$completed} sukses · " . ($totalRuns - $completed) . " gagal")
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color($totalRuns > 0 ? 'info' : 'gray'),

            Stat::make('Avg Match Score', $avgScore > 0 ? number_format($avgScore, 1) . '/100' : '—')
                ->description($avgScore >= 70 ? 'Pool kandidat kuat' : ($avgScore >= 50 ? 'Pool sedang' : 'Perlu sourcing lebih'))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($avgScore >= 70 ? 'success' : ($avgScore >= 50 ? 'warning' : 'danger')),

            Stat::make('Top Provider', $topProvider?->adapter_name ?? '—')
                ->description($topProvider ? "{$topProvider->count} runs · Rp " . number_format($topProvider->total_cost ?? 0, 0, ',', '.') : 'Belum ada')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('warning'),

            Stat::make('Strong Candidates', $strongCandidates)
                ->description('Recommendation strong_yes / yes')
                ->descriptionIcon('heroicon-m-trophy')
                ->color($strongCandidates > 0 ? 'success' : 'gray'),
        ];
    }

    public static function canView(): bool
    {
        return AIScreening::query()->exists() || auth()->user()?->is_super_admin;
    }
}
