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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'exivo' => [
        'username' => env('EXIVO_USERNAME'),
        'password' => env('EXIVO_PASSWORD'),
        'site_id' => env('EXIVO_SITE_ID'),
        'components' => [
            'main_entrance' => 'beef84d5-ec07-4f07-8967-c19c87b13310',
        ]
    ],

    'infomaniak' => [
        'token' => env('INFOMANIAK_API_TOKEN'),
        'drive_id' => env('INFOMANIAK_DRIVE_ID'),
        'root_directory_id' => env('INFOMANIAK_ROOT_DIRECTORY_ID'),
    ]

];
