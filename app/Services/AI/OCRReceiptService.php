<?php

namespace App\Services\AI;

use App\Adapters\AdapterRegistry;
use App\Adapters\Contracts\LLMAdapterInterface;
use App\Models\ReimbursementRequest;
use Illuminate\Support\Facades\Storage;

/**
 * OCR Receipt — extract data dari foto struk reimbursement menggunakan vision LLM.
 *
 * Pakai LLM dengan vision capability (GPT-4 Vision, Claude vision, Gemini vision).
 * Owner BYOK provider via adapter config.
 */
class OCRReceiptService
{
    public function __construct(private AdapterRegistry $registry) {}

    /**
     * Extract: vendor name, total amount, date, items, category guess.
     */
    public function extract(string $imagePath, int $tenantId): array
    {
        $adapter = $this->registry->resolveDefaultForDomain($tenantId, 'llm');
        if (!$adapter instanceof LLMAdapterInterface) {
            return ['error' => 'Belum ada LLM provider'];
        }

        $imageBase64 = base64_encode(Storage::disk('local')->get($imagePath));
        $dataUri = "data:image/jpeg;base64,{$imageBase64}";

        $result = $adapter->complete([
            ['role' => 'system', 'content' => 'Kamu adalah OCR engine untuk struk Indonesia. Extract data dan return JSON only.'],
            ['role' => 'user', 'content' => [
                ['type' => 'text', 'text' => 'Extract dari struk ini:
{
  "vendor": "<nama merchant>",
  "total": <total integer dalam Rupiah, tanpa titik/koma>,
  "date": "<YYYY-MM-DD>",
  "items": [{"name": "...", "qty": 1, "price": 0}],
  "category_guess": "<transport|meals|medical|entertainment|other>",
  "confidence": <0-100>
}
Return JSON only, tanpa wrap markdown.'],
                ['type' => 'image_url', 'image_url' => ['url' => $dataUri]],
            ]],
        ]);

        $content = preg_replace('/```(json)?/', '', $result['content']);
        $parsed = json_decode(trim($content), true);

        return is_array($parsed) ? $parsed : ['error' => 'Failed to parse', 'raw' => $result['content']];
    }
}
