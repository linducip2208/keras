<?php

namespace App\Services\Overtime;

/**
 * Overtime calculator sesuai Kepmenaker 102/2004.
 * Hari kerja: jam 1 = 1.5x, jam 2+ = 2x upah/jam
 * Hari libur (6 hari kerja): jam 1-7 = 2x, jam 8 = 3x, jam 9-10 = 4x
 * Hari libur (5 hari kerja): jam 1-8 = 2x, jam 9 = 3x, jam 10-11 = 4x
 * Upah/jam = upah_bulanan / 173
 */
class OvertimeCalculator
{
    public function calculate(float $monthlySalary, float $hours, string $type = 'workday', int $workDaysPerWeek = 5): array
    {
        $hourlyRate = $monthlySalary / 173;
        $total = 0;
        $breakdown = [];

        if ($type === 'workday') {
            // Jam 1: 1.5x
            $h1 = min(1.0, $hours);
            $total += $h1 * 1.5 * $hourlyRate;
            $breakdown[] = sprintf('Jam ke-1: %.2f x 1.5 x %.2f = %.0f', $h1, $hourlyRate, $h1 * 1.5 * $hourlyRate);

            // Jam 2+: 2x
            $h2 = max(0, $hours - 1.0);
            if ($h2 > 0) {
                $total += $h2 * 2 * $hourlyRate;
                $breakdown[] = sprintf('Jam ke-2+: %.2f x 2 x %.2f = %.0f', $h2, $hourlyRate, $h2 * 2 * $hourlyRate);
            }
        } else {
            // Hari libur — bergantung work_days_per_week
            $base1 = $workDaysPerWeek === 5 ? 8 : 7; // jam pertama yang 2x
            $base2 = $workDaysPerWeek === 5 ? 1 : 1; // jam berikutnya 3x
            // selebihnya 4x

            $h1 = min((float)$base1, $hours);
            $total += $h1 * 2 * $hourlyRate;
            $breakdown[] = sprintf('Libur jam 1-%d: %.2f x 2 x %.2f = %.0f', $base1, $h1, $hourlyRate, $h1 * 2 * $hourlyRate);

            $h2 = min((float)$base2, max(0, $hours - $base1));
            if ($h2 > 0) {
                $total += $h2 * 3 * $hourlyRate;
                $breakdown[] = sprintf('Libur jam %d: %.2f x 3 x %.2f = %.0f', $base1+1, $h2, $hourlyRate, $h2 * 3 * $hourlyRate);
            }

            $h3 = max(0, $hours - $base1 - $base2);
            if ($h3 > 0) {
                $total += $h3 * 4 * $hourlyRate;
                $breakdown[] = sprintf('Libur jam %d+: %.2f x 4 x %.2f = %.0f', $base1+$base2+1, $h3, $hourlyRate, $h3 * 4 * $hourlyRate);
            }
        }

        return [
            'hourly_rate' => $hourlyRate,
            'hours' => $hours,
            'type' => $type,
            'amount' => round($total, 0),
            'breakdown' => $breakdown,
        ];
    }
}
