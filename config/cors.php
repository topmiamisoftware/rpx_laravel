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
<<<<<<< HEAD
    //Uncomment this for dev and make sure you include your localhost in this list.*/
<<<<<<< HEAD
    /*'allowed_origins' => [  'https://localhost:4200',
                            'https://192.168.1.66:4200',
    ],*/
    'allowed_origins' => [
            'https://spotbie.com',
            'https://demo.spotbie.com',
            'https://spotbie-demo.netlify.app',
            'https://spotbie-master.netlify.app',
            'https://spotbie-staging.netlify.app'
=======
=======
>>>>>>> f8f7a8a156dd8f239c8819d1e4e1dc6c613e9424
    'allowed_origins' => ['https://spotbie.com',
                            'https://localhost',
                            'app://localhost',
                            'https://spotbie-master.netlify.app',
>>>>>>> 18d6b465f0d3dc22f380da7a63cfc3045d0c3afe
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true
];
