<?php

namespace App\Services\AI;

use App\Adapters\AdapterRegistry;
use App\Adapters\Contracts\LLMAdapterInterface;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\JobPosting;

/**
 * AI CV Screening — score candidate vs job posting menggunakan LLM (BYOK).
 *
 * Owner pilih adapter LLM (OpenAI-compatible) di Settings > Integrations.
 * Service ini resolve adapter aktif dan call complete() dengan prompt khusus.
 */
class CVScreeningService
{
    public function __construct(private AdapterRegistry $registry) {}

    public function scoreApplication(Application $application, ?string $model = null): array
    {
        $application->loadMissing(['candidate', 'jobPosting']);
        $job = $application->jobPosting;
        $candidate = $application->candidate;

        $cvText = $candidate->ai_summary ?: $this->extractCvText($candidate);
        $jdText = "Posisi: {$job->title}\n\nDeskripsi:\n{$job->description}\n\nRequirements:\n{$job->requirements}";

        $adapter = $this->resolveAdapter($job->tenant_id);
        if (!$adapter) {
            return ['error' => 'Belum ada LLM provider yang dikonfigurasi. Tambah di Settings > Integrations.'];
        }

        $result = $adapter->complete([
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ['role' => 'user', 'content' => "JOB:\n{$jdText}\n\nCV/RESUME:\n{$cvText}\n\nReturn JSON only."],
        ], $model);

        $parsed = $this->parseScoreResponse($result['content']);

        $application->update([
            'ai_match_score' => $parsed['score'] ?? null,
        ]);
        $candidate->update([
            'ai_summary' => $parsed['summary'] ?? $candidate->ai_summary,
            'ai_score' => $parsed['score'] ?? $candidate->ai_score,
        ]);

        return [
            'score' => $parsed['score'] ?? null,
            'summary' => $parsed['summary'] ?? null,
            'strengths' => $parsed['strengths'] ?? [],
            'weaknesses' => $parsed['weaknesses'] ?? [],
            'recommendation' => $parsed['recommendation'] ?? null,
            'usage' => $result['usage'] ?? null,
        ];
    }

    private function systemPrompt(): string
    {
        return <<<PROMPT
Kamu adalah AI recruiter expert untuk pasar Indonesia. Tugasmu screening CV vs job description.

Output WAJIB dalam JSON valid (tanpa markdown wrapper) dengan struktur:
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
PROMPT;
    }

    private function parseScoreResponse(string $content): array
    {
        // Strip markdown code fence if present
        $content = preg_replace('/```(json)?\s*([\s\S]+?)\s*```/i', '$2', $content);
        $data = json_decode(trim($content), true);
        return is_array($data) ? $data : ['summary' => $content, 'score' => null];
    }

    private function extractCvText(Candidate $candidate): string
    {
        // TODO: future enhancement — extract text from PDF CV using OCR adapter
        return "Nama: {$candidate->full_name}\n" .
               "Posisi saat ini: {$candidate->current_position}\n" .
               "Pengalaman: {$candidate->years_experience} tahun\n" .
               "Lokasi: {$candidate->location}";
    }

    private function resolveAdapter(int $tenantId): ?LLMAdapterInterface
    {
        $adapter = $this->registry->resolveDefaultForDomain($tenantId, 'llm');
        return $adapter instanceof LLMAdapterInterface ? $adapter : null;
    }
}
