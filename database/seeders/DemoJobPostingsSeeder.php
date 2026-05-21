<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoJobPostingsSeeder extends Seeder
{
    public function run(): void
    {
        $tenantId = DB::table('tenants')->where('slug', 'acme')->value('id');
        $companyId = DB::table('companies')->where('tenant_id', $tenantId)->value('id');
        $depts = DB::table('departments')->where('tenant_id', $tenantId)->pluck('id', 'code');
        if (!$tenantId) return;

        $jobs = [
            ['Senior Software Engineer (Backend)', 'engineering-backend', 'ENG', 'Jakarta', 'full_time', 18_000_000, 28_000_000,
                "Kami mencari Senior Backend Engineer yang passionate untuk join tim Engineering kami. Anda akan terlibat dalam design & implementasi sistem yang scale ke ribuan user, mentor junior engineers, dan kolaborasi dengan tim Product.",
                "- 5+ tahun pengalaman Laravel atau framework PHP modern lainnya\n- Solid di MySQL/PostgreSQL design + query optimization\n- Familiar dengan queue, cache (Redis), background job\n- Experience dengan microservice / event-driven (nice to have)\n- Bahasa Inggris business level"],
            ['Mobile Developer (Flutter)', 'mobile-flutter', 'ENG', 'Jakarta / Remote', 'full_time', 14_000_000, 22_000_000,
                "Build mobile app HRIS untuk karyawan Indonesia. Stack: Flutter + Riverpod + Dio. Anda akan kerja close dengan Designer UI/UX dan Backend untuk delivery feature-feature mobile-first.",
                "- 3+ tahun Flutter (Dart)\n- Familiar dengan native channel iOS + Android\n- Experience dengan offline-first apps + sync logic\n- Pernah publish app ke App Store + Play Store\n- Familiar dengan Firebase / FCM"],
            ['Product Manager — HRIS', 'product-manager-hris', 'MKT', 'Jakarta', 'full_time', 20_000_000, 35_000_000,
                "Lead product strategy untuk modul HRIS kami. Anda akan kerja dengan founder, customer success, dan engineering untuk prioritize roadmap dan deliver outcome bisnis nyata.",
                "- 4+ tahun PM experience, idealnya di SaaS B2B\n- Familiar dengan HR domain Indonesia (PPh21, BPJS, UU CK)\n- Strong analytical: SQL, mixpanel/amplitude, A/B testing\n- Data-driven decision making\n- Indonesian + English fluency"],
            ['Sales Account Executive', 'sales-account-executive', 'SLS', 'Jakarta', 'full_time', 8_000_000, 15_000_000,
                "Close deal HRIS ke perusahaan Indonesia 20-200 karyawan. Anda dapat hot leads dari marketing + dapat full komisi untuk deal yang Anda close.",
                "- 2+ tahun B2B sales (HRIS/SaaS pengalaman plus)\n- Komunikasi excellent (Bahasa Indonesia)\n- Bisa demo product confidently\n- Komisi: 5-10% dari ARR closed deal\n- Komuter Jakarta atau willing to travel"],
            ['HR Generalist — Internal', 'hr-generalist-internal', 'HRD', 'Jakarta', 'full_time', 7_000_000, 12_000_000,
                "Kelola internal HR untuk tim kami (saat ini 25, target 60 EOY). Pakai HRIS produk kami sendiri (eat your own dog food). Recruitment, onboarding, payroll, performance review.",
                "- 3+ tahun HR generalist di startup/SMB\n- Familiar dengan PPh21, BPJS, UU CK\n- Pengalaman pakai HRIS modern (Talenta/Kantorku/dll plus)\n- Bahasa Inggris business level\n- Detail-oriented + empati"],
            ['Designer UI/UX', 'designer-ui-ux', 'MKT', 'Jakarta / Remote', 'full_time', 10_000_000, 18_000_000,
                "Design fitur HRIS yang HRD beneran pakai tanpa training. Focus ke UX research, prototype, dan delivery design system yang scalable.",
                "- 3+ tahun product design (Figma master)\n- Portfolio shows shipped products, bukan cuma concept\n- UX research skill (interview, usability test)\n- Familiar dengan Filament/admin design patterns plus\n- Bisa hands-on dengan Tailwind/CSS"],
        ];

        foreach ($jobs as [$title, $slug, $deptCode, $location, $type, $salaryMin, $salaryMax, $desc, $req]) {
            DB::table('job_postings')->insert([
                'tenant_id' => $tenantId,
                'company_id' => $companyId,
                'department_id' => $depts[$deptCode] ?? null,
                'title' => $title,
                'slug' => $slug,
                'description' => $desc,
                'requirements' => $req,
                'location' => $location,
                'employment_type' => $type,
                'salary_min' => $salaryMin,
                'salary_max' => $salaryMax,
                'salary_currency' => 'IDR',
                'status' => 'published',
                'published_at' => now()->subDays(rand(1, 30)),
                'public_token' => Str::lower(Str::random(16)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Demo: 6 job postings seeded dengan public token untuk apply.');
    }
}
