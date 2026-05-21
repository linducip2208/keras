<?php

namespace App\Adapters\Contracts;

interface BankAdapterInterface extends AdapterInterface
{
    /**
     * Generate batch file content from disbursement items.
     * @param array<int, array{account_number:string, account_holder:string, amount:float, ref:string}> $items
     * @return array{content:string, filename:string, mime:string}
     */
    public function generateBatchFile(array $items, array $meta = []): array;
}
