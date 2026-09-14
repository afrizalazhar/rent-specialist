<?php

return [

    'whatsapp_number' => env('WHATSAPP_NUMBER', null),

    'business' => [
        'name'     => env('BUSINESS_NAME', 'Rent Specialist'),
        'address'  => env('BUSINESS_ADDRESS', 'Jl. Contoh No. 123, Jakarta'),
        'phone'    => env('BUSINESS_PHONE', '+62 812-3456-7890'),
        'email'    => env('BUSINESS_EMAIL', 'halo@rent.local'),
        'hours'    => env('BUSINESS_HOURS', 'Senin–Minggu, 08.00–21.00'),
        'map_url'  => env('BUSINESS_MAP_URL', 'https://maps.google.com/?q=-6.200,106.816'),
    ],

];
