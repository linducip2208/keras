<?php

namespace App\Services\Tax;

use App\Models\PtkpRule;
use App\Models\TerRate;

/**
 * PPh 21 calculator pakai Tarif Efektif Rata-rata (TER) sesuai PMK 168/2023.
 * Berlaku Januari 2024+. Default kategori A (TK/0..K/1). Kategori B/C: di-seed sesuai PMK.
 */
class PPh21TerCalculator
{
    public function __construct(private int $year = 2026) {}

    /** Override tahun regulasi (mis. dari tanggal periode payroll). */
    public function setYear(int $year): static
    {
        $this->year = $year;
        return $this;
    }

    public function calculate(float $monthlyGross, string $ptkpStatus): array
    {
        $category = $this->resolveCategory($ptkpStatus);
        $rate = $this->resolveTerRate($monthlyGross, $category);
        $pph21 = round($monthlyGross * $rate / 100, 0);

        return [
            'gross_basis' => $monthlyGross,
            'ptkp_status' => $ptkpStatus,
            'ter_category' => $category,
            'ter_rate' => $rate,
            'pph_21' => $pph21,
            'breakdown' => "PPh 21 TER (kat $category) = $monthlyGross x $rate% = $pph21",
        ];
    }

    public function annualPtkp(string $status): float
    {
        return (float) (PtkpRule::query()
            ->where('year', $this->year)
            ->where('status', $status)
            ->value('amount') ?? 54_000_000);
    }

    private function resolveCategory(string $ptkp): string
    {
        // PMK 168/2023: Kategori A = TK/0, TK/1, K/0
        //               Kategori B = TK/2, TK/3, K/1, K/2
        //               Kategori C = K/3
        return match ($ptkp) {
            'TK_0', 'TK_1', 'K_0' => 'A',
            'TK_2', 'TK_3', 'K_1', 'K_2' => 'B',
            'K_3' => 'C',
            default => 'A',
        };
    }

    private function resolveTerRate(float $gross, string $category): float
    {
        $bracket = TerRate::query()
            ->where('year', $this->year)
            ->where('category', $category)
            ->where('bracket_from', '<=', $gross)
            ->where(function ($q) use ($gross) {
                $q->whereNull('bracket_to')->orWhere('bracket_to', '>=', $gross);
            })
            ->orderByDesc('bracket_from')
            ->first();

        return $bracket ? (float) $bracket->rate_percent : 0.0;
    }
}
