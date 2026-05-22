<?php

return [
    'enabled' => env('WATI_ENABLED', false),

    'api_endpoint' => env('WATI_API_ENDPOINT'),

    'api_token' => env('WATI_API_TOKEN'),

    'channel_phone_number' => env('WATI_CHANNEL_PHONE_NUMBER'),

    'template_name' => env('WATI_TEMPLATE_NAME', 'support_desk'),

    'broadcast_name' => env('WATI_BROADCAST_NAME', 'support_desk'),

    /**
     * Wati template parameter names to send (must match placeholders in your Wati template).
     */
    'template_parameters' => ['name'],
];
