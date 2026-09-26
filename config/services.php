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
    'google_drive' => [
        'credentials_path' => storage_path('app/private/gdrive-service.json'),
    ],
    'influxdb' => [
        'host'        => env('INFLUXDB_HOST', 'localhost'),
        'port'        => env('INFLUXDB_PORT', 8086),
        'ssl'         => env('INFLUXDB_SSL', false),
        'verify_ssl'  => env('INFLUXDB_VERIFY_SSL', false),
        'timeout'     => env('INFLUXDB_TIMEOUT', 1),
        'token'       => env('INFLUXDB_TOKEN'),
        'org'         => env('INFLUXDB_ORG', env('INFLUXDB_ORGANISATION')),
        'bucket'      => env('INFLUXDB_BUCKET', env('INFLUXDB_DEFAULT_BUCKET')),
    ],

];
