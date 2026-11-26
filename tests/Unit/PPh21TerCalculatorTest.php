<?php

namespace Tests\Unit;

use App\Models\TerRate;
use App\Services\Tax\PPh21TerCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TER PMK 168/2023: tarif dikenakan LANGSUNG ke bruto bulanan,
 * TANPA pengurang biaya jabatan / iuran (itu hanya untuk koreksi tahunan).
 */
class PPh21TerCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private function seedRates(): void
    {
        TerRate::create(['year' => 2026, 'category' => 'A', 'bracket_from' => 0, 'bracket_to' => 5_400_000, 'rate_percent' => 0]);
        TerRate::create(['year' => 2026, 'category' => 'A', 'bracket_from' => 5_400_001, 'bracket_to' => 5_650_000, 'rate_percent' => 0.25]);
        TerRate::create(['year' => 2026, 'category' => 'A', 'bracket_from' => 5_650_001, 'bracket_to' => null, 'rate_percent' => 0.5]);
    }

    public function test_ter_applied_directly_to_monthly_gross(): void
    {
        $this->seedRates();
        $calc = new PPh21TerCalculator(2026);

        // Bruto 6 juta kategori A → bracket 0,5%
        $r = $calc->calculate(6_000_000, 'TK_0');
        $this->assertEquals('A', $r['ter_category']);
        $this->assertEquals(0.5, $r['ter_rate']);
        $this->assertEquals(30_000, $r['pph_21']);
        // gross_basis HARUS bruto asli, bukan bruto-setelah-pengurang
        $this->assertEquals(6_000_000, $r['gross_basis']);
    }

    public function test_ter_zero_bracket(): void
    {
        $this->seedRates();
        $calc = new PPh21TerCalculator(2026);
        $r = $calc->calculate(5_000_000, 'TK_0');
        $this->assertEquals(0, $r['pph_21']);
    }

    public function test_category_mapping(): void
    {
        $this->seedRates();
        $calc = new PPh21TerCalculator(2026);
        $this->assertEquals('B', $calc->calculate(6_000_000, 'TK_2')['ter_category']);
        $this->assertEquals('C', $calc->calculate(6_000_000, 'K_3')['ter_category']);
    }
}
