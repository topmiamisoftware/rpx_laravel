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

    'paths' => ['*'],

    'allowed_methods' => ['*'],

    //Uncomment this for dev and make sure you include your localhost in this list.*/
    /*'allowed_origins' => [  'https://localhost:4200',
                            'https://192.168.1.66:4200',
    ],*/
    'allowed_origins' => [
                            'https://demo.spotbie.com',
                            'https://localhost',
                            'https://localhost:4200'
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true
];
