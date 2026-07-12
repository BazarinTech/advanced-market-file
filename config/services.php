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

    'palpluss' => [
        'base_url'     => env('PALPLUSS_BASE_URL', 'https://api.palpluss.com/v1'),
        'auth'         => env('PALPLUSS_AUTH'),
        'channel_id'   => env('PALPLUSS_CHANNEL_ID'),
        'callback_url' => env('PALPLUSS_CALLBACK_URL'),
    ],

    'links' => [
        'whatsapp_group'   => env('WHATSAPP_GROUP_URL'),
        'app_download'     => env('APP_DOWNLOAD_URL'),
        'customer_support' => env('CUSTOMER_SUPPORT_URL'),
    ],

    'support' => [
        'email'   => env('SUPPORT_EMAIL'),
        'phone'   => env('SUPPORT_PHONE'),
        'network' => env('SUPPORT_NETWORK', 'Safaricom'),
    ],

];
