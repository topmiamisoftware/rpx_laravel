<?php


    use Illuminate\Support\Facades\App;

    $environment = App::environment();

    $frontEndIp = 'https://spotbie.com/';

    if (App::environment('local')) {
        // The environment is local
        $frontEndIp = env('SPOTBIE_DEV_FRONT_END_IP', 'https://192.168.1.65:4200/');
    }

    if (App::environment('staging')) {
        // The environment is staging...
        $frontEndIp = env('SPOTBIE_STAGING_FRONT_END_IP', 'https://spotbie-staging.netlify.app/');
    }

    if (App::environment('production')) {
        // The environment is production...
        $frontEndIp = env('SPOTBIE_PROD_FRONT_END_IP', 'https://spotbie.com/');
    }

    return [
        'my_loc_x' => env('MY_LOC_X', '0.0'),
        'my_loc_y' => env('MY_LOC_Y', '0.0'),
        'my_address' => env('MY_ADDRESS', '55 North Pole Dr. 99th Igloo ave.'),
        'default_images_path' => env('DEFAULT_IMAGES_PATH', 'https://api.spotbie.com/defaults/'),
        'background_images_path' => env('BACKGROUND_IMAGES_PATH', 'https://api.spotbie.com/backgrounds/'),
        'business_pass_key' => env('SPOTBIE_BUSINESS_PASSKEY'),
        'rewards_images_path' => env('REWARDS_IMAGES_PATH', 'https://api.spotbie.com/rewards-media/images/'),
        'my_business_categories' => ['Barbeque', 'Burgers', 'Brunch'],
        'spotbie_front_end_ip' => $frontEndIp
    ];