<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Jawaly SMS Configuration
    |--------------------------------------------------------------------------
    */

    // Your Jawaly SMS API Key (Username)
    'username' => env('JAWALY_SMS_API_KEY', ''),

    // Your Jawaly SMS API Secret (Password)
    'password' => env('JAWALY_SMS_API_SECRET', ''),

    // Default sender name (should be pre-approved by Jawaly)
    'default_sender' => env('JAWALY_SMS_SENDER', '4jawaly'),
];
