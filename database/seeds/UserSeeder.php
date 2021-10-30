<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\SpotbieUser;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class UserSeeder extends Seeder
{
    public function run()
    {

        User::factory()
        ->count(10)
        ->hasSpotbieUser(1, function (array $attributes){
            return [
                'user_type' => 4,
            ]; 
        })
        ->hasLoyaltyPointBalance(1)
        ->create();

        User::factory()
        ->count(10)
        ->hasSpotbieUser(1, function (array $attributes){
            return [
                'user_type' => rand(1,2),
            ];                        
        })
        ->hasBusiness(1,function (array $attributes, User $user) {

            $businessPhotoFolder = 'assets/images/def/places-to-eat/';

            switch($user->spotbieUser->user_type){
                case '1':
                    $businessPhotoFolder = 'assets/images/def/places-to-eat/';
                    break;
                case '2':
                    $businessPhotoFolder = 'assets/images/def/shopping/';
                    break;
                case '3':
                    $businessPhotoFolder = 'assets/images/def/events/';
                    break;   
            }
    
            $businessPhoto = config('spotbie.spotbie_front_end_ip') . $businessPhotoFolder . rand(1,25) . '.jpg';

            return [
                'photo' => $businessPhoto
            ];

        })
        ->hasLoyaltyPointBalance(1)
        ->create();
    }
    
}
