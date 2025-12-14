<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    'api_path' => 'api',
    'api_domain' => null,
    'export_path' => 'api.json',
    'info' => [
        'version' => env('API_VERSION', '1.0.0'),
        'description' => 'Authenticated REST API for trips, itineraries, journal entries, and stats. Secured via Sanctum token abilities.',
    ],
    'ui' => [
        'title' => env('APP_NAME', 'Travel Journal').' API',
        'theme' => 'light',
        'hide_try_it' => false,
        'hide_schemas' => false,
        'logo' => '',
        'try_it_credentials_policy' => 'include',
        'layout' => 'responsive',
    ],
    'servers' => [
        'Local' => 'api',
    ],
    'enum_cases_description_strategy' => 'description',
    'enum_cases_names_strategy' => false,
    'flatten_deep_query_parameters' => true,
    'middleware' => [
        'web',
        RestrictedDocsAccess::class,
        'auth',
    ],
    'extensions' => [],
];
