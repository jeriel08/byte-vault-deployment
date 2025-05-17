<?php

return [
    'paths' => ['api/*', 'reports/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [env('APP_URL', 'https://byte-vault.onrender.com')],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
