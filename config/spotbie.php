<?php

    return [
        'my_loc_x' => env('MY_LOC_X', '0.0'),
        'my_loc_y' => env('MY_LOC_Y', '0.0'),
        'my_address' => env('MY_ADDRESS', '55 North Pole Dr. 99th Igloo ave.'),
        'default_images_path' => env('DEFAULT_IMAGES_PATH', 'https://api.spotbie.com/defaults/'),
        'background_images_path' => env('BACKGROUND_IMAGES_PATH', 'https://api.spotbie.com/backgrounds/'),
        'business_pass_key' => env('SPOTBIE_BUSINESS_PASSKEY'),
        'rewards_images_path' => env('REWARDS_IMAGES_PATH', 'https://api.spotbie.com/rewards-media/images/'),
        'my_business_categories_food' => ['Barbeque', 'Burgers', 'Brunch'],
        'my_business_categories_shopping' => ['Baby Gear', 'Bridal', 'Clothing'],
        'my_business_categories_events' => ['Music'],        
        'spotbie_front_end_ip' => env('SPOTBIE_FRONT_END_IP', 'https://spotbie-staging.netlify.app/')
    ];