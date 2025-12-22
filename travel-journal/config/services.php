<?php

return [
    'slack' => [
        'webhook_url' => env('SLACK_WEBHOOK_URL'),
    ],

    'sendgrid' => [
        'api_key' => env('SENDGRID_API_KEY'),
        'from_address' => env('MAIL_FROM_ADDRESS'),
        'from_name' => env('MAIL_FROM_NAME'),
    ],

    'sentry' => [
        'dsn' => env('SENTRY_LARAVEL_DSN'),
        'sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0.0),
        'profiles_sample_rate' => (float) env('SENTRY_PROFILES_SAMPLE_RATE', 0.0),
    ],

    'google_analytics' => [
        'property_id' => env('GA4_PROPERTY_ID'),
        'credentials' => env('GOOGLE_APPLICATION_CREDENTIALS'),
        'measurement_id' => env('GA_MEASUREMENT_ID'),
        'debug_mode' => (bool) env('GA_DEBUG_MODE', false),
    ],

    'unsplash' => [
        'access_key' => env('UNSPLASH_ACCESS_KEY'),
    ],
];
