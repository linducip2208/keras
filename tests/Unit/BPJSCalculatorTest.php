<?php

namespace Tests\Unit;

use App\Models\BpjsRate;
use App\Services\BPJS\BPJSCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Iuran BPJS: tarif × upah (capped). Angka acuan: Kes 4%+1% cap 12jt,
 * JHT 3,7%+2%, JP 2%+1% cap 10.042.300, JKK 0,3%? no — JKK 0,24–1,74% employer,
 * JKM 0,3% employer.
 */
class BPJSCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private function seedRates(): void
    {
        $rows = [
            ['program' => 'kesehatan', 'employer_rate' => 0.04, 'employee_rate' => 0.01, 'salary_cap' => 12_000_000],
            ['program' => 'jht', 'employer_rate' => 0.037, 'employee_rate' => 0.02, 'salary_cap' => null],
            ['program' => 'jp', 'employer_rate' => 0.02, 'employee_rate' => 0.01, 'salary_cap' => 10_042_300],
            ['program' => 'jkk', 'employer_rate' => 0.0054, 'employee_rate' => 0, 'salary_cap' => null],
            ['program' => 'jkm', 'employer_rate' => 0.003, 'employee_rate' => 0, 'salary_cap' => null],
        ];
        foreach ($rows as $r) {
            BpjsRate::create($r + ['year' => 2026, 'effective_from' => '2026-01-01', 'effective_to' => '2026-12-31']);
        }
    }

    public function test_health_cap_applies(): void
    {
        $this->seedRates();
        $calc = new BPJSCalculator(2026);
        // Basis 20jt → kesehatan di-cap di 12jt
        $r = $calc->calculateAll(20_000_000, 0.0054);
        $this->assertEquals(12_000_000, $r['breakdown']['kesehatan']['basis']);
        $this->assertEquals(480_000, $r['breakdown']['kesehatan']['employer_amount']);
        $this->assertEquals(120_000, $r['breakdown']['kesehatan']['employee_amount']);
    }

    public function test_pension_cap_applies(): void
    {
        $this->seedRates();
        $calc = new BPJSCalculator(2026);
        $r = $calc->calculateAll(20_000_000, 0.0054);
        $this->assertEquals(10_042_300, $r['breakdown']['jp']['basis']);
    }

    public function test_totals_match_breakdown(): void
    {
        $this->seedRates();
        $calc = new BPJSCalculator(2026);
        $r = $calc->calculateAll(8_000_000, 0.0054);
        $emp = $r['breakdown']['kesehatan']['employee_amount']
            + $r['breakdown']['jht']['employee_amount']
            + $r['breakdown']['jp']['employee_amount'];
        $this->assertEquals($emp, $r['total_employee']);
        $this->assertGreaterThan(0, $r['total_employer']);
    }
}
