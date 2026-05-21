<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoApplicationsSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = DB::table('tenants')->where('slug', 'acme')->value('id');
        if (!$tenantId) return;

        $jobs = DB::table('job_postings')->where('tenant_id', $tenantId)->get();
        if ($jobs->isEmpty()) return;

        $candidates = [
            ['Reza Pratama Wijaya', 'reza.pratama@email.com', '081234567001', 'Jakarta', 'Software Engineer', 5, 'https://linkedin.com/in/reza-pratama'],
            ['Dewi Sartika Lestari', 'dewi.sartika@email.com', '081234567002', 'Bandung', 'Senior Full Stack Developer', 7, 'https://linkedin.com/in/dewi-sartika'],
            ['Hendra Kurniawan', 'hendra.k@email.com', '081234567003', 'Surabaya', 'Mobile Developer (Flutter)', 4, 'https://linkedin.com/in/hendra-k'],
            ['Maya Putri Handayani', 'maya.putri@email.com', '081234567004', 'Yogyakarta', 'Product Manager', 6, 'https://linkedin.com/in/maya-putri'],
            ['Bagas Aditya Saputra', 'bagas.aditya@email.com', '081234567005', 'Jakarta', 'Backend Engineer (PHP/Laravel)', 8, 'https://linkedin.com/in/bagas-aditya'],
            ['Sinta Permatasari', 'sinta.p@email.com', '081234567006', 'Jakarta', 'UI/UX Designer', 5, 'https://linkedin.com/in/sinta-p'],
            ['Iqbal Maulana', 'iqbal.m@email.com', '081234567007', 'Medan', 'Sales Executive', 3, 'https://linkedin.com/in/iqbal-m'],
            ['Larasati Anggraini', 'lara.a@email.com', '081234567008', 'Jakarta', 'HR Generalist', 4, 'https://linkedin.com/in/lara-a'],
            ['Fahmi Rizki Pratama', 'fahmi.rp@email.com', '081234567009', 'Bandung', 'Junior Developer', 1, 'https://linkedin.com/in/fahmi-rp'],
            ['Annisa Maharani', 'annisa.m@email.com', '081234567010', 'Semarang', 'Marketing Specialist', 2, 'https://linkedin.com/in/annisa-m'],
        ];

        $coverLetters = [
            'Saya tertarik posisi ini karena experience saya di Laravel + Vue cocok dengan stack tim. Pernah lead project re-architecture monolith ke microservice di company sebelumnya.',
            'Punya 7 tahun experience sebagai full-stack engineer dengan focus ke Laravel + React. Pernah build HRIS feature dari nol untuk client enterprise di sektor manufaktur.',
            'Flutter developer dengan 4 tahun experience, sudah publish 6 apps ke Play Store. Familiar dengan offline-first architecture + state management Riverpod.',
            'Product Manager dengan track record ship 4 products. Familiar dengan HR domain Indonesia (sudah handle compliance PPh21 di product sebelumnya).',
            'Backend specialist Laravel, 8 tahun experience. Pernah build payroll calculation engine untuk 10K+ karyawan dengan PPh21 TER 2024.',
            'UI/UX designer dengan portfolio HRIS + fintech. Pernah research user behavior untuk app yang user-base mayoritas blue collar.',
            'Sales experience B2B SaaS, 3 tahun. Familiar dengan menjual ke perusahaan SMB Indonesia segmen 50-200 karyawan.',
            'HR generalist 4 tahun, familiar dengan PPh21, BPJS, UU CK. Pernah implementasi HRIS Talenta + Mekari Jurnal end-to-end.',
            'Junior developer eager to learn, basic Laravel + React. Internship 1 tahun di startup fintech.',
            'Marketing specialist focus ke B2B content. Punya track record growth blog dari 0 ke 50K monthly visits dalam 12 bulan.',
        ];

        $now = now();
        foreach ($candidates as $i => [$name, $email, $phone, $location, $position, $exp, $linkedin]) {
            $candId = DB::table('candidates')->insertGetId([
                'tenant_id' => $tenantId,
                'full_name' => $name,
                'email' => $email,
                'phone' => $phone,
                'location' => $location,
                'current_position' => $position,
                'years_experience' => $exp,
                'linkedin_url' => $linkedin,
                'source' => 'career_page',
                'created_at' => $now->copy()->subDays(rand(1, 30)),
                'updated_at' => $now,
            ]);

            $job = $jobs->random();
            DB::table('applications')->insert([
                'tenant_id' => $tenantId,
                'candidate_id' => $candId,
                'job_posting_id' => $job->id,
                'cover_letter' => $coverLetters[$i] ?? '-',
                'salary_expectation' => rand(8, 25) * 1_000_000,
                'notice_period_days' => collect([14, 30, 60, 0])->random(),
                'status' => 'active',
                'applied_at' => $now->copy()->subDays(rand(1, 30)),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $this->command->info('Demo: 10 candidates + 10 applications seeded untuk AI screening test.');
    }
}
