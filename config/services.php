<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'bunnycdn' => [
        'storage_zone' => env('BUNNYCDN_STORAGE_ZONE', 'pbvsi'),
        'access_key'   => env('BUNNYCDN_ACCESS_KEY', '6f8b3df9-5063-4a9c-bdb7dec01911-1a05-418e'),
        'hostname'     => env('BUNNYCDN_HOSTNAME', 'storage.bunnycdn.com'),
        'base_url'     => env('BUNNYCDN_BASE_URL', 'https://cdn.pbvsi.or.id/'),
    ],

];
