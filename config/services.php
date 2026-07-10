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
    | NatX Firebase Cloud Messaging (HTTP v1).
    | google-services.json is for the mobile app only.
    | Server needs a Firebase service account JSON file.
    */
    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID', 'natx-da485'),
        'credentials' => env('FIREBASE_CREDENTIALS', 'storage/app/firebase/natx-firebase-credentials.json'),
    ],

];
