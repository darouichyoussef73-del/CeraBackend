<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-origin resource sharing (CORS) configuration
    |--------------------------------------------------------------------------
    |
    | For local development set `allowed_origins` to your Next.js origin
    | (for example: http://localhost:3000) and ensure `supports_credentials`
    | is true so cookies can be sent cross-site.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Provide a comma-separated list in .env, e.g. "http://localhost:3000,https://app.example.com"
    'allowed_origins' => array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000')))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
