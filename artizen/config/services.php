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

    'whatsapp' => [
        'provider' => env('WHATSAPP_PROVIDER', null), // e.g. twilio, meta, interakt, msg91
        'api_key' => env('WHATSAPP_API_KEY', null),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID', null),
        'from_number' => env('WHATSAPP_FROM_NUMBER', null),
    ],

    'sms' => [
        'provider' => env('SMS_PROVIDER', null),
        'api_key' => env('SMS_API_KEY', null),
        'sender_id' => env('SMS_SENDER_ID', null),
    ],

];
