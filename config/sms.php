<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS provider
    |--------------------------------------------------------------------------
    |
    | arkesel — Arkesel HTTP API (single API key; common in Ghana)
    | hubtel  — Hubtel JSON API (requires client id + secret)
    | log     — Log only (safe for local development)
    |
    */

    'provider' => env('SMS_PROVIDER', 'log'),

    'api_key' => env('SMS_API_KEY', ''),

    'sender' => env('SMS_SENDER', 'DCAPPLE'),

    'arkesel_url' => env('SMS_ARKESEL_URL', 'https://sms.arkesel.com/sms/api'),

    'hubtel' => [
        'send_url' => env('SMS_HUBTEL_SEND_URL', 'https://sms.hubtel.com/v1/messages/send'),
        'client_id' => env('SMS_HUBTEL_CLIENT_ID', ''),
        'client_secret' => env('SMS_HUBTEL_CLIENT_SECRET', ''),
    ],

];
