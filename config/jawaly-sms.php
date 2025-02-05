<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Jawaly SMS Configuration
    |--------------------------------------------------------------------------
    */

    // Your Jawaly SMS account username
    'username' => env('JAWALY_SMS_USERNAME', ''),

    // Your Jawaly SMS account password
    'password' => env('JAWALY_SMS_PASSWORD', ''),

    // Default sender name (should be pre-approved by Jawaly)
    'default_sender' => env('JAWALY_SMS_SENDER', ''),

    // Enable debug mode for testing (optional)
    'debug' => env('JAWALY_SMS_DEBUG', false),
];
