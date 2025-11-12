<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'midtrans' => [
        'serverKey'     => "SB-Mid-server-GkW-oS9nOpd2CktkXZve26qV",
        'clientKey'     => "SB-Mid-client-_63DbX6J3paRjarh",
        'isProduction'  => false,
        'isSanitized'   => true,
        'is3ds'         => true,
    ],

    'biteship' => [
        'api_key' => env('BITESHIP_API_KEY', 'biteship_test.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoiY2hhc3RldGVzdGluZyIsInVzZXJJZCI6IjY5MTRiZTQ4YTU0YmRmNzdjYTJhMTE3OSIsImlhdCI6MTc2Mjk2ODU1Mn0.CIFCKrBBgaMmy5A4ahc1zunuleTJRE_TfZ9bH6TSirg'),
    ],

];
