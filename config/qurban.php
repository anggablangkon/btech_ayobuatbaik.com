<?php

return [
    'voucher' => [
        'venue_name' => env('QURBAN_VOUCHER_VENUE', 'Masjid Fauget'),
        'pickup_date' => env('QURBAN_VOUCHER_DATE', '10 Dzulhijjah 1445 H'),
        'pickup_time' => env('QURBAN_VOUCHER_TIME', '10.00 - 14.00 WIB'),
        'footer_note' => env(
            'QURBAN_VOUCHER_FOOTER',
            '*Kupon ini harap dibawa ketika akan mengambil daging qurban'
        ),
    ],
];
