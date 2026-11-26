<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tarif PPN untuk invoice SaaS (operator-side billing)
    |--------------------------------------------------------------------------
    | NEEDS ACCOUNTANT VALIDATION: tarif umum 11% dipakai sebagai default.
    | PPN 12% (PMK 131/2024) berlaku untuk barang/jasa tertentu — konfirmasi
    | ke konsultan pajak sebelum mengubah untuk jasa SaaS.
    */
    'ppn_rate' => (float) env('BILLING_PPN_RATE', 0.11),
];
