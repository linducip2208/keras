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
            if ($adapterConfig->tenant_id !== $application->tenant_id) {
                throw new \RuntimeException('Adapter bukan milik tenant lamaran ini.');
            }

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
        $header = "Nama: {$candidate->full_name}\n" .
               "Posisi saat ini: " . ($candidate->current_position ?: '-') . "\n" .
               "Pengalaman: " . ($candidate->years_experience ?: 0) . " tahun\n" .
               "Lokasi: " . ($candidate->location ?: '-') . "\n" .
               "LinkedIn: " . ($candidate->linkedin_url ?: '-') . "\n" .
               "Cover Letter:\n{$coverLetter}";

        $fileText = $this->extractFileText($candidate->cv_path);

        return $header . "\n\nIsi CV (file):\n" . $fileText;
    }

    /**
     * Ekstrak teks dari file CV apa adanya (tanpa library eksternal):
     * - txt/md/csv/log → dibaca langsung
     * - docx → XML document.xml di-strip tag (tanpa gambar/tabel kompleks)
     * - pdf/doc → TIDAK didukung parser native; dikembalikan marker jujur
     *   NEEDS-PDF-PARSER agar AI tidak menilai dari data kosong.
     */
    private function extractFileText(?string $cvPath): string
    {
        if (!$cvPath) {
            return '[tidak ada file CV terlampir]';
        }

        try {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if (!$disk->exists($cvPath)) {
                return '[file CV tidak ditemukan di storage: ' . basename($cvPath) . ']';
            }

            $ext = strtolower(pathinfo($cvPath, PATHINFO_EXTENSION));

            if (in_array($ext, ['txt', 'md', 'csv', 'log'], true)) {
                $text = $disk->get($cvPath);
                return $this->truncateText($text);
            }

            if ($ext === 'docx') {
                return $this->truncateText($this->extractDocx($disk->path($cvPath)));
            }

            return '[NEEDS-PDF-PARSER: file .' . $ext . ' belum bisa dibaca otomatis — '
                . 'penilaian hanya dari metadata + cover letter. Upload versi .docx/.txt agar akurat.]';
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('CV text extract gagal: ' . $e->getMessage());
            return '[gagal membaca file CV: ' . $e->getMessage() . ']';
        }
    }

    private function extractDocx(string $absPath): string
    {
        $zip = new \ZipArchive();
        if ($zip->open($absPath) !== true) {
            return '[file .docx rusak / tidak bisa dibuka]';
        }
        $xml = $zip->getFromName('word/document.xml') ?: '';
        $zip->close();
        if (!$xml) {
            return '[dokumen .docx kosong]';
        }
        $text = preg_replace('/<w:p[^>]*>/i', "\n", strip_tags($xml));
        return trim(preg_replace('/[ \t]+/', ' ', html_entity_decode($text ?? '')));
    }

    private function truncateText(string $text, int $max = 6000): string
    {
        $text = trim($text);
        if (mb_strlen($text) <= $max) {
            return $text === '' ? '[file CV kosong]' : $text;
        }
        return mb_substr($text, 0, $max) . "\n[...dipotong, file lebih panjang...]";
    }
}
