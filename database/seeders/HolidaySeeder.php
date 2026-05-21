<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays2026 = [
            ['2026-01-01', 'Tahun Baru Masehi', 'national'],
            ['2026-02-17', 'Isra Mikraj Nabi Muhammad SAW', 'religious'],
            ['2026-02-19', 'Tahun Baru Imlek 2577', 'religious'],
            ['2026-03-20', 'Hari Raya Nyepi (Tahun Baru Saka 1948)', 'religious'],
            ['2026-04-03', 'Wafat Yesus Kristus', 'religious'],
            ['2026-04-05', 'Hari Paskah', 'religious'],
            ['2026-05-01', 'Hari Buruh Internasional', 'national'],
            ['2026-05-14', 'Kenaikan Yesus Kristus', 'religious'],
            ['2026-05-21', 'Hari Raya Waisak 2570', 'religious'],
            ['2026-05-26', 'Idul Fitri 1447 H (Hari 1)', 'religious'],
            ['2026-05-27', 'Idul Fitri 1447 H (Hari 2)', 'religious'],
            ['2026-06-01', 'Hari Lahir Pancasila', 'national'],
            ['2026-08-02', 'Idul Adha 1447 H', 'religious'],
            ['2026-08-17', 'Hari Kemerdekaan RI', 'national'],
            ['2026-08-22', 'Tahun Baru Islam 1448 H', 'religious'],
            ['2026-10-31', 'Maulid Nabi Muhammad SAW', 'religious'],
            ['2026-12-25', 'Hari Raya Natal', 'religious'],
            // Cuti bersama (sample - real one updated by SKB)
            ['2026-05-25', 'Cuti Bersama Idul Fitri', 'cuti_bersama'],
            ['2026-05-28', 'Cuti Bersama Idul Fitri', 'cuti_bersama'],
            ['2026-05-29', 'Cuti Bersama Idul Fitri', 'cuti_bersama'],
            ['2026-12-24', 'Cuti Bersama Natal', 'cuti_bersama'],
            ['2026-12-26', 'Cuti Bersama Natal', 'cuti_bersama'],
        ];

        foreach ($holidays2026 as [$date, $name, $type]) {
            DB::table('holidays')->updateOrInsert(
                ['country_code' => 'ID', 'date' => $date],
                [
                    'year' => 2026,
                    'name' => $name,
                    'type' => $type,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
