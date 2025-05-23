<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    #'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'paths' => ['api/*','login', '/graphql', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:3000',
        'http://192.168.100.17:3100',
        //'http://192.168.100.17:3000',
        'http://192.168.100.17:8020',
        'https://ws.alguiensabe.lat',
        'http://192.168.100.216:3000',
        'https://panel.alguiensabe.lat'
    ],
    'allowed_origins_patterns' => ['.*'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
