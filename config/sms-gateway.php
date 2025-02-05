<?php

return [
    'api_url' => env('SMS_GATEWAY_API_URL', 'https://api.example.com/send'),
    'balance_url' => env('SMS_GATEWAY_BALANCE_URL', 'https://api.example.com/balance'),
    'senders_url' => env('SMS_GATEWAY_SENDERS_URL', 'https://api.example.com/senders'),
    'username' => env('SMS_GATEWAY_USERNAME', ''),
    'password' => env('SMS_GATEWAY_PASSWORD', ''),
];
