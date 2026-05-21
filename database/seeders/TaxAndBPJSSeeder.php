<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaxAndBPJSSeeder extends Seeder
{
    public function run(): void
    {
        // PTKP 2026 (sama dengan 2024 - belum berubah)
        $ptkp = [
            'TK_0' => 54_000_000, 'TK_1' => 58_500_000, 'TK_2' => 63_000_000, 'TK_3' => 67_500_000,
            'K_0'  => 58_500_000, 'K_1'  => 63_000_000, 'K_2'  => 67_500_000, 'K_3'  => 72_000_000,
        ];
        foreach ($ptkp as $status => $amount) {
            DB::table('ptkp_rules')->updateOrInsert(
                ['year' => 2026, 'status' => $status],
                ['amount' => $amount, 'description' => "PTKP 2026 - {$status}", 'updated_at' => now(), 'created_at' => now()]
            );
        }

        // TER 2024 (PMK 168/2023) - Kategori A (TK/0..K/1)
        $terA = [
            [0, 5_400_000, 0],
            [5_400_000, 5_650_000, 0.0025], // 0.25%
            [5_650_000, 5_950_000, 0.005],
            [5_950_000, 6_300_000, 0.0075],
            [6_300_000, 6_750_000, 0.01],
            [6_750_000, 7_500_000, 0.0125],
            [7_500_000, 8_550_000, 0.015],
            [8_550_000, 9_650_000, 0.0175],
            [9_650_000, 10_050_000, 0.02],
            [10_050_000, 10_350_000, 0.0225],
            [10_350_000, 10_700_000, 0.025],
            [10_700_000, 11_050_000, 0.03],
            [11_050_000, 11_600_000, 0.035],
            [11_600_000, 12_500_000, 0.04],
            [12_500_000, 13_750_000, 0.05],
            [13_750_000, 15_100_000, 0.06],
            [15_100_000, 16_950_000, 0.07],
            [16_950_000, 19_750_000, 0.08],
            [19_750_000, 24_150_000, 0.09],
            [24_150_000, 26_450_000, 0.1],
            [26_450_000, 28_000_000, 0.11],
            [28_000_000, 30_050_000, 0.12],
            [30_050_000, 32_400_000, 0.13],
            [32_400_000, 35_400_000, 0.14],
            [35_400_000, 39_100_000, 0.15],
            [39_100_000, 43_850_000, 0.16],
            [43_850_000, 47_800_000, 0.17],
            [47_800_000, 51_400_000, 0.18],
            [51_400_000, 56_300_000, 0.19],
            [56_300_000, 62_200_000, 0.20],
            [62_200_000, 68_600_000, 0.21],
            [68_600_000, 77_500_000, 0.22],
            [77_500_000, 89_000_000, 0.23],
            [89_000_000, 103_000_000, 0.24],
            [103_000_000, 125_000_000, 0.25],
            [125_000_000, 157_000_000, 0.26],
            [157_000_000, 206_000_000, 0.27],
            [206_000_000, 337_000_000, 0.28],
            [337_000_000, 454_000_000, 0.29],
            [454_000_000, 550_000_000, 0.30],
            [550_000_000, 695_000_000, 0.31],
            [695_000_000, 910_000_000, 0.32],
            [910_000_000, 1_400_000_000, 0.33],
            [1_400_000_000, null, 0.34],
        ];

        foreach ($terA as [$from, $to, $rate]) {
            DB::table('ter_rates')->insert([
                'year' => 2026,
                'category' => 'A',
                'bracket_from' => $from,
                'bracket_to' => $to,
                'rate_percent' => $rate * 100,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        // NOTE: Kategori B & C bisa di-seed full di production, untuk demo Kategori A sufficient

        // BPJS Rates 2026
        $bpjs = [
            ['kesehatan', 0.04, 0.01, 12_000_000, '4% employer + 1% employee, cap upah 12 juta'],
            ['jht', 0.037, 0.02, null, '3.7% employer + 2% employee'],
            ['jp', 0.02, 0.01, 10_042_300, '2% employer + 1% employee, cap upah 10.042.300 (2026)'],
            ['jkk', 0.0054, 0, null, 'Default risk-medium 0.54% employer (range 0.24%-1.74%)'],
            ['jkm', 0.003, 0, null, '0.3% employer'],
        ];
        foreach ($bpjs as [$program, $emp, $empl, $cap, $notes]) {
            DB::table('bpjs_rates')->updateOrInsert(
                ['year' => 2026, 'program' => $program, 'effective_from' => '2026-01-01'],
                [
                    'effective_to' => null,
                    'employer_rate' => $emp,
                    'employee_rate' => $empl,
                    'salary_cap' => $cap,
                    'notes' => $notes,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // Bank Master
        $banks = [
            ['014', 'Bank Central Asia (BCA)', 'CENAIDJA', true, 'bca', true],
            ['008', 'Bank Mandiri', 'BMRIIDJA', true, 'mandiri', true],
            ['009', 'Bank Negara Indonesia (BNI)', 'BNINIDJA', true, 'bni', true],
            ['002', 'Bank Rakyat Indonesia (BRI)', 'BRINIDJA', true, 'bri', true],
            ['022', 'Bank CIMB Niaga', 'BNIAIDJA', true, 'cimb', true],
            ['013', 'Bank Permata', 'BBBAIDJA', true, 'permata', false],
            ['011', 'Bank Danamon', 'BDINIDJA', true, 'danamon', false],
            ['200', 'Bank BTN', 'BTANIDJA', false, null, false],
            ['016', 'Bank Maybank Indonesia', 'IBBKIDJA', false, null, false],
            ['426', 'Bank Mega', 'MEGAIDJA', false, null, false],
            ['046', 'Bank DBS Indonesia', 'DBSBIDJA', false, null, false],
            ['542', 'Bank Jago', null, false, null, false],
            ['501', 'Bank SeaBank', null, false, null, false],
            ['523', 'Bank Allo', null, false, null, false],
        ];
        foreach ($banks as [$code, $name, $swift, $batch, $batchFmt, $api]) {
            DB::table('bank_master')->updateOrInsert(
                ['code' => $code],
                [
                    'name' => $name,
                    'swift' => $swift,
                    'supports_batch_file' => $batch,
                    'batch_format' => $batchFmt,
                    'supports_instant_api' => $api,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
