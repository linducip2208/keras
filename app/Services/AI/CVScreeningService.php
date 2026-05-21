<?php

namespace App\Services\AI;

use App\Adapters\AdapterRegistry;
use App\Adapters\Contracts\LLMAdapterInterface;
use App\Models\AdapterConfig;
use App\Models\AIScreening;
use App\Models\Application;
use App\Models\Candidate;

/**
 * AI CV Screening — multi-provider, reactive.
 *
 * Setiap call screen() = 1 row AIScreening baru (history kept).
 * UI bisa show comparison side-by-side dari multiple AI providers.
 */
class CVScreeningService
{
    public function __construct(private AdapterRegistry $registry) {}

    public function screen(Application $application, AdapterConfig $adapterConfig, ?string $model = null, ?int $triggeredByUserId = null): AIScreening
    {
        $application->loadMissing(['candidate', 'jobPosting']);
        $job = $application->jobPosting;
        $candidate = $application->candidate;

        $screening = AIScreening::create([
            'tenant_id' => $application->tenant_id,
            'application_id' => $application->id,
            'adapter_config_id' => $adapterConfig->id,
            'adapter_name' => $adapterConfig->name,
            'model_used' => $model,
            'status' => 'running',
            'triggered_by_user_id' => $triggeredByUserId,
        ]);

        try {
            $adapter = $this->registry->resolve($adapterConfig->id);
            if (!$adapter instanceof LLMAdapterInterface) {
                throw new \RuntimeException('Adapter ' . $adapterConfig->name . ' bukan LLM adapter');
            }

            $cvText = $this->extractCvText($candidate, $application);
            $jdText = "Posisi: {$job->title}\n\nDeskripsi:\n{$job->description}\n\nRequirements:\n{$job->requirements}";

            $result = $adapter->complete([
                ['role' => 'system', 'content' => $this->systemPrompt()],
                ['role' => 'user', 'content' => "JOB:\n{$jdText}\n\nCV/RESUME:\n{$cvText}\n\nReturn JSON only."],
            ], $model);

            $parsed = $this->parseScoreResponse($result['content']);

            $screening->update([
                'model_used' => $result['model'] ?? $model,
                'score' => $parsed['score'] ?? null,
                'summary' => $parsed['summary'] ?? null,
                'strengths' => $parsed['strengths'] ?? [],
                'weaknesses' => $parsed['weaknesses'] ?? [],
                'recommendation' => $parsed['recommendation'] ?? null,
                'reasoning' => $parsed['reasoning'] ?? null,
                'raw_response' => $parsed,
                'usage' => $result['usage'] ?? null,
                'status' => 'completed',
            ]);

            // Update application + candidate dgn latest score
            $application->update(['ai_match_score' => $parsed['score'] ?? null]);
            $candidate->update([
                'ai_score' => $parsed['score'] ?? $candidate->ai_score,
                'ai_summary' => $parsed['summary'] ?? $candidate->ai_summary,
            ]);
        } catch (\Throwable $e) {
            $screening->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }

        return $screening->fresh();
    }

    public function comparison(Application $application): \Illuminate\Database\Eloquent\Collection
    {
        return AIScreening::where('application_id', $application->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function bestPerProvider(Application $application): \Illuminate\Support\Collection
    {
        return AIScreening::where('application_id', $application->id)
            ->where('status', 'completed')
            ->orderByDesc('score')
            ->get()
            ->groupBy('adapter_config_id')
            ->map(fn ($group) => $group->first());
    }

    public function availableAdapters(int $tenantId): \Illuminate\Database\Eloquent\Collection
    {
        return AdapterConfig::where('tenant_id', $tenantId)
            ->where('domain', 'llm')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function systemPrompt(): string
    {
        return <<<PROMPT
Kamu adalah AI recruiter expert untuk pasar Indonesia. Tugasmu screening CV vs job description.

Output WAJIB dalam JSON valid (tanpa markdown wrapper):
{
  "score": <0-100>,
  "summary": "<1-2 kalimat ringkasan kandidat>",
  "strengths": ["<bullet1>", "<bullet2>", "<bullet3>"],
  "weaknesses": ["<bullet1>", "<bullet2>"],
  "recommendation": "strong_yes|yes|maybe|no|strong_no",
  "reasoning": "<1 paragraph alasan score>"
}

Pertimbangkan: skill match, years of experience, education relevance, project portfolio,
lokasi (jika job spesifik kota), salary expectation vs range, language fluency.
Jangan diskriminatif (gender, agama, ras). Fokus murni kompetensi profesional.
Response dalam bahasa Indonesia.
PROMPT;
    }

    private function parseScoreResponse(string $content): array
    {
        $content = preg_replace('/```(json)?\s*([\s\S]+?)\s*```/i', '$2', $content);
        $data = json_decode(trim($content), true);
        return is_array($data) ? $data : ['summary' => $content, 'score' => null];
    }

    private function extractCvText(Candidate $candidate, ?Application $application = null): string
    {
        $coverLetter = $application?->cover_letter ?: '-';
        return "Nama: {$candidate->full_name}\n" .
               "Posisi saat ini: " . ($candidate->current_position ?: '-') . "\n" .
               "Pengalaman: " . ($candidate->years_experience ?: 0) . " tahun\n" .
               "Lokasi: " . ($candidate->location ?: '-') . "\n" .
               "LinkedIn: " . ($candidate->linkedin_url ?: '-') . "\n" .
               "Cover Letter:\n{$coverLetter}";
    }
}
