<?php

return [

    'name' => env('CURRENCY_NAME', 'Indonesian Rupiah'),
    'code' => env('CURRENCY_CODE', 'IDR'),
    'symbol' => env('CURRENCY_SYMBOL', 'Rp'),

    // Money is stored as an integer of the smallest unit.
    // IDR has no subunits, so 1 rupiah = 1 unit.
    'subunit_divisor' => 1,

];
