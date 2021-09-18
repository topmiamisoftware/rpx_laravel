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
        ->count(150)
        ->hasSpotbieUser(1)
        ->hasUserLocation(1)
        ->hasWebOptions(1)
        ->hasContactMe(1)
        ->hasDefaultImages(1,function (array $attributes, User $user) {
            return ['default_image_url' => $user->spotbieUser->default_picture];
        })
        ->create();

        /*
        User::factory()
        ->hasSpotbieUser(1,[
            'first_name' => 'Franco',
            'last_name' => 'Petitfour',
            'description' => 'Hello my name is Franco Petitfour. Welcome to my SpotBie profile!',
        ])
        ->hasUserLocation(1)
        ->hasWebOptions(1)
        ->hasContactMe(1)
        ->hasDefaultImages(1,function (array $attributes, User $user) {
            return ['default_image_url' => $user->spotbieUser->default_picture];
        })
        ->create([
            'username' => '0456fra', 
            'email' => 'franco.petitfour001@gmail.com', 
            'password' => Hash::make('HelloWorld33!')
        ]);*/

        for($i = 0; $i < 150; $i++){
            Friendship::factory()
            ->create();
        }

    }
}
