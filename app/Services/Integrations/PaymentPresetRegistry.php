<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Cache;

/**
 * Loads payment gateway presets dari storage/app/integration-presets/payment/*.json
 * Per CLAUDE.md §2 — preset hanya untuk autofill convenience saat owner pilih provider.
 * Code TIDAK PERNAH reference file ini saat runtime untuk execute (yg execute adalah
 * AdapterRegistry → format-based class).
 */
class PaymentPresetRegistry
{
    private const PRESET_DIR = 'integration-presets/payment';

    public function all(): array
    {
        return Cache::remember('payment_presets', 600, function () {
            $dir = storage_path('app/' . self::PRESET_DIR);
            if (!is_dir($dir)) return [];

            $regions = [];
            foreach (glob($dir . '/*.json') as $file) {
                $data = json_decode(file_get_contents($file), true);
                if (!is_array($data)) continue;
                $regions[] = $data;
            }
            return $regions;
        });
    }

    /**
     * Flat list semua gateway dengan tag region.
     */
    public function flatList(): array
    {
        $result = [];
        foreach ($this->all() as $region) {
            foreach ($region['gateways'] ?? [] as $g) {
                $result[] = array_merge($g, ['region' => $region['region']]);
            }
        }
        return $result;
    }

    public function findBySlug(string $slug): ?array
    {
        foreach ($this->flatList() as $g) {
            if (($g['slug'] ?? null) === $slug) return $g;
        }
        return null;
    }

    /**
     * Options untuk Filament Select dengan grouped by region.
     * @return array<string, array<string, string>>
     */
    public function groupedOptions(): array
    {
        $groups = [];
        foreach ($this->all() as $region) {
            $key = $region['region'];
            foreach ($region['gateways'] ?? [] as $g) {
                $label = $g['name'];
                if (!empty($g['vendor']) && $g['vendor'] !== $g['name']) {
                    $label .= " — {$g['vendor']}";
                }
                $groups[$key][$g['slug']] = $label;
            }
        }
        return $groups;
    }

    public function clearCache(): void
    {
        Cache::forget('payment_presets');
    }
}
