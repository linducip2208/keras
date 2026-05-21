<?php

namespace App\Services\BPJS;

use App\Models\BpjsRate;

/**
 * Calculator BPJS sesuai aturan Indonesia 2026:
 * - Kesehatan: 4% employer + 1% employee, cap upah 12 juta
 * - JHT: 3.7% employer + 2% employee
 * - JP: 2% employer + 1% employee, cap upah 10.042.300 (2026)
 * - JKK: 0.24%-1.74% employer (per risk level)
 * - JKM: 0.3% employer
 */
class BPJSCalculator
{
    public function __construct(private int $year = 2026) {}

    public function calculateAll(float $bpjsBasis, ?float $jkkRate = null): array
    {
        $results = [];
        $totalEmployer = 0;
        $totalEmployee = 0;

        foreach (['kesehatan', 'jht', 'jp', 'jkk', 'jkm'] as $program) {
            $rate = BpjsRate::where('year', $this->year)->where('program', $program)->first();
            if (!$rate) continue;

            $cappedBasis = $rate->salary_cap ? min($bpjsBasis, (float) $rate->salary_cap) : $bpjsBasis;
            $employerPct = $program === 'jkk' && $jkkRate ? (float) $jkkRate : (float) $rate->employer_rate;
            $employerAmount = round($cappedBasis * $employerPct, 0);
            $employeeAmount = round($cappedBasis * (float) $rate->employee_rate, 0);

            $results[$program] = [
                'basis' => $cappedBasis,
                'employer_rate' => $employerPct,
                'employee_rate' => (float) $rate->employee_rate,
                'employer_amount' => $employerAmount,
                'employee_amount' => $employeeAmount,
            ];

            $totalEmployer += $employerAmount;
            $totalEmployee += $employeeAmount;
        }

        return [
            'total_employer' => $totalEmployer,
            'total_employee' => $totalEmployee,
            'breakdown' => $results,
        ];
    }
}
