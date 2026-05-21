<?php

namespace App\Adapters\Bank;

use App\Adapters\Contracts\BankAdapterInterface;

/**
 * BCA KlikBCA Bisnis batch file generator (CSV format).
 * Format: HEADER + record per transaksi.
 * Format SAMPLE (verify dengan iBank tenant masing-masing — bank format kadang berubah):
 *   Account_From,Beneficiary_Account,Beneficiary_Name,Amount,Description
 */
class BCABatchFileAdapter implements BankAdapterInterface
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
        return ['ok' => !empty($this->companyAccount), 'message' => 'BCA config OK'];
    }

    public function generateBatchFile(array $items, array $meta = []): array
    {
        $lines = ['Account_From,Beneficiary_Account,Beneficiary_Name,Amount,Description'];
        foreach ($items as $item) {
            $lines[] = sprintf(
                '%s,%s,%s,%s,%s',
                $this->companyAccount,
                $this->csvEscape($item['account_number']),
                $this->csvEscape($item['account_holder']),
                number_format($item['amount'], 0, '', ''),
                $this->csvEscape($item['ref'] ?? $this->description),
            );
        }

        $period = $meta['period'] ?? date('Ym');
        return [
            'content' => implode("\r\n", $lines),
            'filename' => "BCA_PAYROLL_{$period}.csv",
            'mime' => 'text/csv',
        ];
    }

    private function csvEscape(string $s): string
    {
        if (str_contains($s, ',') || str_contains($s, '"')) {
            return '"' . str_replace('"', '""', $s) . '"';
        }
        return $s;
    }
}
