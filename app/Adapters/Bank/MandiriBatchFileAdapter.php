<?php

namespace App\Adapters\Bank;

use App\Adapters\Contracts\BankAdapterInterface;

/**
 * Mandiri Cash Management batch file (TXT pipe-delimited).
 * Verify exact spec dengan iBank Mandiri tenant masing-masing.
 */
class MandiriBatchFileAdapter implements BankAdapterInterface
{
    private string $companyAccount = '';
    private string $description = 'Payroll';

    public function configure(array $config): void
    {
        $this->companyAccount = $config['company_account'] ?? '';
        $this->description = $config['description'] ?? 'Payroll';
    }

    public function test(): array
    {
        return ['ok' => !empty($this->companyAccount), 'message' => 'Mandiri config OK'];
    }

    public function generateBatchFile(array $items, array $meta = []): array
    {
        $period = $meta['period'] ?? date('Ym');
        $lines = [];

        // Header
        $lines[] = sprintf('H|%s|%s|%d|%s', $this->companyAccount, $period, count($items), $this->description);

        // Records
        foreach ($items as $idx => $item) {
            $lines[] = sprintf(
                'D|%d|%s|%s|%s|%s',
                $idx + 1,
                $item['account_number'],
                str_replace('|', ' ', $item['account_holder']),
                number_format($item['amount'], 0, '', ''),
                $item['ref'] ?? $this->description,
            );
        }

        // Trailer
        $total = array_sum(array_column($items, 'amount'));
        $lines[] = sprintf('T|%d|%s', count($items), number_format($total, 0, '', ''));

        return [
            'content' => implode("\r\n", $lines),
            'filename' => "MANDIRI_PAYROLL_{$period}.txt",
            'mime' => 'text/plain',
        ];
    }
}
