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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'facebook' => [
        'app_id' => env('FB_APP_ID'),
        'app_secret' => env('FB_APP_SECRET'),
        'access_token' => env('FB_ACCESS_TOKEN'),
        'lead_form_id' => env('FB_LEAD_FORM_ID'),
    ],

    /*
    | Incoming Meta WhatsApp / Natdemy webhook credentials.
    | Callers must send X-API-Key and X-API-Secret headers.
    */
    'natdemy_webhook' => [
        'api_key' => env('NATDEMY_API_KEY'),
        'api_secret' => env('NATDEMY_API_SECRET'),
    ],

    /*
    | LMS course sync / streams (CRM → LMS API).
    | Header: X-CRM-API-KEY
    */
    'lms' => [
        'api_key' => env('CRM_API_KEY'),
        'courses_url' => env('LMS_COURSES_API_URL'),
        'streams_url' => env('LMS_STREAMS_API_URL'),
        'leads_url' => env('LMS_LEADS_API_URL'),
    ],

    /*
    | NatX Firebase Cloud Messaging (HTTP v1).
    | google-services.json is for the mobile app only.
    | Server needs a Firebase service account JSON file.
    */
    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID', 'natx-da485'),
        'credentials' => env('FIREBASE_CREDENTIALS', 'storage/app/firebase/natx-firebase-credentials.json'),
    ],

];
