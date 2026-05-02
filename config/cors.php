<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'webhook/github'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [
        '^https?:\/\/([a-z0-9\-]+)\.up\.railway\.app$',
        '^https?:\/\/([a-z0-9\-]+)\.localhost(:\d+)?$',
    ],

    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
