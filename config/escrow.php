<?php

return [
    'base_url' => env('ESCROW_BASE_URL', 'https://api.escrow.com'),

    'default_path' => env('ESCROW_DEFAULT_PATH', '2017-09-01'),

    'pay_path' => env('ESCROW_PAY_PATH', 'integration/pay/2018-03-31'),

    'email' => env('ESCROW_EMAIL', ''),

    'api_key' => env('ESCROW_API_KEY', ''),

    'currencies_supported' => [
        'usd', 'aud', 'euro', 'gbp', 'and', 'cad'
    ],

    'default_currency' => 'usd',

    'webhook_verification_key' => env('ESCROW_WEBHOOK_VERIFICATION_KEY', null),
];
