<?php

namespace App\Adapters\Contracts;

interface AdapterInterface
{
    public function configure(array $config): void;
    public function test(): array;
}
