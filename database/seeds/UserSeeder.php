<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Factories\UserFactory;

use App\Models\User;
use App\Models\SpotbieUser;
use App\Models\Friendship;
use App\Models\ContactMe;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class UserSeeder extends Seeder
{
    public function run()
    {

        User::factory()
        ->count(20)
        ->hasSpotbieUser(1)
        ->hasLoyaltyPointBalance(1)
        ->create();

        /*
        
        Uncomment this if you want to Create a custom user.

        User::factory()
        ->hasSpotbieUser(1,[
            'first_name' => 'YourName',
            'last_name' => 'YourLastName',
            'description' => 'Hello my name is YourName YourLastName. Welcome to my SpotBie profile!',
        ])
        ->hasUserLocation(1)
        ->hasDefaultImages(1,function (array $attributes, User $user) {
            return ['default_image_url' => $user->spotbieUser->default_picture];
        })
        ->create([
            'username' => 'yourusernam3', 
            'email' => 'youremail@gmail.com', 
            'password' => Hash::make('HelloWorld33!')
        ]);*/

    }
}
