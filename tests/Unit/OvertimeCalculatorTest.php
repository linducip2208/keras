<?php

namespace Tests\Unit;

use App\Services\Overtime\OvertimeCalculator;
use PHPUnit\Framework\TestCase;

/**
 * Skenario Kepmenaker 102/2004. Upah/jam = gaji bulanan / 173.
 */
class OvertimeCalculatorTest extends TestCase
{
    private OvertimeCalculator $calc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calc = new OvertimeCalculator();
    }

    public function test_workday_first_hour_is_1_5x(): void
    {
        // 5.000.000 / 173 = 28.901,73/jam; 1 jam = 1,5x
        $r = $this->calc->calculate(5_000_000, 1, 'workday');
        $this->assertEquals(round(5_000_000 / 173 * 1.5), $r['amount']);
    }

    public function test_workday_three_hours(): void
    {
        // jam-1 1,5x + jam 2-3 2x = 5,5x upah/jam
        $r = $this->calc->calculate(5_000_000, 3, 'workday');
        $this->assertEquals(round(5_000_000 / 173 * 5.5), $r['amount']);
    }

    public function test_holiday_five_day_week_eight_hours(): void
    {
        // 8 jam pertama 2x = 16x upah/jam
        $r = $this->calc->calculate(5_000_000, 8, 'holiday', 5);
        $this->assertEquals(round(5_000_000 / 173 * 16), $r['amount']);
    }

    public function test_holiday_five_day_week_ten_hours(): void
    {
        // 8x2 + 1x3 + 1x4 = 23x upah/jam
        $r = $this->calc->calculate(5_000_000, 10, 'holiday', 5);
        $this->assertEquals(round(5_000_000 / 173 * 23), $r['amount']);
    }

    public function test_holiday_six_day_week(): void
    {
        // 7x2 + 1x3 + 2x4 = 25x upah/jam
        $r = $this->calc->calculate(5_000_000, 10, 'holiday', 6);
        $this->assertEquals(round(5_000_000 / 173 * 25), $r['amount']);
    }

    public function test_zero_hours_zero_pay(): void
    {
        $r = $this->calc->calculate(5_000_000, 0, 'workday');
        $this->assertEquals(0, $r['amount']);
    }
}
