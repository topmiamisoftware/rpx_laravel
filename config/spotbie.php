<?php

    $foodCategories = ['Asian Fusion', 'Bagels', 'Bakery', 'Bar', 'Barbeque', 'Breakfast', 'British',
    'Brunch', 'Buffets', 'Burgers', 'Cajun/Creole', 'Caribbean', 'Coffee/Espresso', 'Country Food', 'Cuban',
    'Deli', 'Doughnuts', 'Family Fare', 'Fast Food', 'Fine Dining', 'Food Trucks', 'French', 'German',
    'Gluten-free', 'Greek', 'Happy Hour', 'Hot Dogs', 'Ice Cream', 'Indian', 'Irish', 'Italian',
    'Japanese', 'Latin American', 'Live Entertainment', 'Mediterranean', 'Mexican', 'Nouvelle', 'Pancakes/Waffles', 'Pizza',
    'Polish', 'Sandwiches', 'Seafood', 'Soul Food', 'Soup & Salad', 'Southern', 'Spanish',
    'Sports Bar', 'Steaks', 'Sushi', 'Tapas', 'Thai', 'Vegan Friendly', 'Vegetarian'];

    $shoppingCategories = ['Antiques', 'Art Galleries', 'Arts & Crafts', 'Auction Houses', 'Baby Gear', 'Battery Stores',
    'Bespoke Clothing', 'Books, Mags, Music & Video', 'Brewing Supplies', 'Bridal', 'Cannabis Dispensaries', 'Clothing', 'Computers', 'Cosmetics & Beauty Supply', 'Customized Merchandise',
    'Department Stores', 'Discount Stores', 'Drones', 'Drugstores', 'Duty-Free Shops', 'Electronics', 'Eyeware & Opticians', 'Farming Equipment',
    'Fashion', 'Fireworks', 'Fitness/Exercise Equipment', 'Flea Markets', 'Flowers & Gifts', 'Gemstones & Minerals', 'Gold Buyers', 'Groceries', 'Guns & Ammo',
    'Head Shops', 'High Fidelity Audio Equipment', 'Hobby Shops', 'Home & Garden', 'Horse Equipment Shops', 'Jewelry', 'Knitting Supplies', 'Livestock Feed & Supply',
    'Luggage', 'Medical Supplies', 'Military Surplus', 'Mobile Phone Accessories', 'Mobile Phones', 'Motorcycle Gear', 'Musical Instruments & Teachers',
    'Office Equipment', 'Outlet Stores', 'Packing Supplies', 'Pawn Shops', 'Perfume', 'Photography Stores & Services', 'Pool & Billiards',
    'Pop-up Shops', 'Props', 'Public Markets', 'Religious Items', 'Safe Stores', 'Safety Equipment', 'Shopping Centers',
    'Souvenir Shops', 'Spiritual Shops', 'Sporting Goods', 'Tabletop Games', 'Teacher Supplies', 'Thrift Stores', 'Tobacco Shops',
    'Toy Stores', 'Trophy Shops', 'Uniforms', 'Used Bookstore', 'Vape Shops', 'Vitamins & Supplements', 'Watches',
    'Wholesale Stores', 'Wigs'];

    $eventCategories = ['Film', 'Arts & Theatre', 'Music', 'Sports', 'Miscellaneous', 'Nonticket'];

    return [
        'my_loc_x' => env('MY_LOC_X', '0.0'),
        'my_loc_y' => env('MY_LOC_Y', '0.0'),
        'my_address' => env('MY_ADDRESS', '55 North Pole Dr. 99th Igloo ave.'),
        'default_images_path' => env('DEFAULT_IMAGES_PATH', 'https://api.spotbie.com/defaults/'),
        'background_images_path' => env('BACKGROUND_IMAGES_PATH', 'https://api.spotbie.com/backgrounds/'),
        'business_pass_key' => env('SPOTBIE_BUSINESS_PASSKEY'),
        'rewards_images_path' => env('REWARDS_IMAGES_PATH', 'https://api.spotbie.com/rewards-media/images/'),
        'ad_images_path' => env('AD_IMAGES_PATH', 'https://api.spotbie.com/ad-media/images/'),
        'my_business_categories_food' => $foodCategories,
        'my_business_categories_shopping' => $shoppingCategories,
        'my_business_categories_events' => $eventCategories,        
        'spotbie_front_end_ip' => env('SPOTBIE_FRONT_END_IP', 'https://spotbie-staging.netlify.app/')
    ];