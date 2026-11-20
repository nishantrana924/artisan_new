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
        'provider' => env('WHATSAPP_PROVIDER', null), // meta, twilio, etc.
        'access_token' => env('WHATSAPP_ACCESS_TOKEN', env('WHATSAPP_API_KEY', null)),
        'api_key' => env('WHATSAPP_API_KEY', env('WHATSAPP_ACCESS_TOKEN', null)),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID', null),
        'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID', null),
        'api_version' => env('WHATSAPP_API_VERSION', 'v21.0'),
        'admin_number' => env('WHATSAPP_ADMIN_NUMBER', env('WHATSAPP_FROM_NUMBER', '919131668156')),
        'from_number' => env('WHATSAPP_FROM_NUMBER', '919131668156'),
        'admin_template' => env('WHATSAPP_ADMIN_TEMPLATE', null),
        'customer_template' => env('WHATSAPP_CUSTOMER_TEMPLATE', null),
        'template_language' => env('WHATSAPP_TEMPLATE_LANGUAGE', 'en'),
        'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN', null),
        'app_secret' => env('WHATSAPP_APP_SECRET', null),
        'timeout' => (int) env('WHATSAPP_TIMEOUT', 6),
    ],

    'sms' => [
        'provider' => env('SMS_PROVIDER', null),
        'api_key' => env('SMS_API_KEY', null),
        'sender_id' => env('SMS_SENDER_ID', null),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
    ],

    'turnstile' => [
        'site_key' => env('CLOUDFLARE_TURNSTILE_SITE_KEY'),
        'secret_key' => env('CLOUDFLARE_TURNSTILE_SECRET_KEY'),
    ],

];
