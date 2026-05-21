<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\JobPosting;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CareerController extends Controller
{
    /**
     * Marketplace mode — list semua lowongan dari semua tenant aktif.
     */
    public function index(Request $request)
    {
        $jobs = JobPosting::query()
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->with(['department', 'company', 'position'])
            ->limit(100)
            ->get();

        // Group by tenant for "Per Perusahaan" section
        $companies = Tenant::query()
            ->whereIn('id', $jobs->pluck('tenant_id')->unique())
            ->withCount(['companies as job_count' => function ($q) {
                $q->join('job_postings', 'job_postings.company_id', '=', 'companies.id')
                  ->where('job_postings.status', 'published');
            }])
            ->orderByDesc('job_count')
            ->get();

        return view('career.index', [
            'jobs' => $jobs,
            'companies' => $companies,
            'mode' => 'marketplace',
            'title' => 'Job Marketplace Indonesia — ' . $jobs->count() . ' Lowongan Kerja Terbuka',
            'description' => 'Marketplace lowongan kerja Indonesia dari berbagai perusahaan. Apply langsung dengan upload CV, no daftar required. Powered by HRIS Indonesia.',
        ]);
    }

    /**
     * Per-tenant career page — show lowongan dari 1 perusahaan saja.
     */
    public function tenant(Request $request, string $slug)
    {
        $tenant = Tenant::where('slug', $slug)->where('status', '!=', 'suspended')->firstOrFail();

        $jobs = JobPosting::query()
            ->where('tenant_id', $tenant->id)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->with(['department', 'company', 'position'])
            ->limit(100)
            ->get();

        return view('career.index', [
            'jobs' => $jobs,
            'companies' => collect(),
            'tenant' => $tenant,
            'mode' => 'tenant',
            'title' => "Karir di {$tenant->name} — " . $jobs->count() . " Lowongan Terbuka",
            'description' => "Lihat semua lowongan kerja di {$tenant->name}. Apply langsung dengan upload CV.",
        ]);
    }

    public function show(string $token)
    {
        $job = JobPosting::query()
            ->where('public_token', $token)
            ->where('status', 'published')
            ->with(['department', 'position', 'company'])
            ->firstOrFail();

        return view('career.show', [
            'job' => $job,
            'title' => "{$job->title} — Lowongan Kerja Indonesia",
            'description' => "Apply untuk posisi {$job->title} di " . ($job->company->name ?? 'Perusahaan Indonesia') . ". " . substr(strip_tags($job->description ?? ''), 0, 150),
        ]);
    }

    public function apply(Request $request, string $token)
    {
        $job = JobPosting::query()
            ->where('public_token', $token)
            ->where('status', 'published')
            ->firstOrFail();

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'current_position' => 'nullable|string|max:255',
            'years_experience' => 'nullable|integer|min:0|max:50',
            'location' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'cover_letter' => 'nullable|string|max:5000',
            'salary_expectation' => 'nullable|numeric|min:0',
            'notice_period_days' => 'nullable|integer|min:0|max:365',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'consent' => 'required|accepted',
        ]);

        // Upload CV
        $cvPath = $request->file('cv')->store('candidates/cv', 'public');

        // Resolve or create candidate
        $candidate = Candidate::firstOrCreate(
            ['tenant_id' => $job->tenant_id, 'email' => $data['email']],
            [
                'full_name' => $data['full_name'],
                'phone' => $data['phone'],
                'current_position' => $data['current_position'] ?? null,
                'years_experience' => $data['years_experience'] ?? null,
                'location' => $data['location'] ?? null,
                'linkedin_url' => $data['linkedin_url'] ?? null,
                'cv_path' => $cvPath,
                'source' => 'career_page',
            ]
        );

        // Update CV jika upload baru
        if ($candidate->wasRecentlyCreated === false) {
            $candidate->update(['cv_path' => $cvPath]);
        }

        // Create application (idempotent: 1 candidate × 1 job)
        $application = Application::firstOrCreate(
            ['tenant_id' => $job->tenant_id, 'candidate_id' => $candidate->id, 'job_posting_id' => $job->id],
            [
                'cover_letter' => $data['cover_letter'] ?? null,
                'salary_expectation' => $data['salary_expectation'] ?? null,
                'notice_period_days' => $data['notice_period_days'] ?? null,
                'status' => 'active',
                'applied_at' => now(),
            ]
        );

        return view('career.success', [
            'job' => $job,
            'application' => $application,
        ]);
    }
}
