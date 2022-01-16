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

    'allowed_origins' => ['*'],

   
    //Uncomment this for dev and make sure you include your localhost in this list.*/
     /*'allowed_origins' => ['https://spotbie.com', 
                            'https://localhost:4200',
                            'https://spotbie-demo.netlify.app',
                            'https://spotbie-master.netlify.app',
                            'https://spotbie-staging.netlify.app',
                            'https://192.168.1.65:4200',
                            'https://192.168.1.64:4200'
                        ],*/
    

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
