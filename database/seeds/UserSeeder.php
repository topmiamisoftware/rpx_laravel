<?php

namespace Database\Seeders;

use App\Models\Reward;
use App\Models\SpotbieUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Factories\UserFactory;

use App\Models\User;

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

        $businessAcccountTypes = ['1', '2'];

        User::factory()
        ->count(10)
        ->hasSpotbieUser(1, function (array $attributes) use ($businessAcccountTypes){
            $key = rand(0,1);
            return [
                'user_type' => $businessAcccountTypes[$key],
            ];                        
        })
        ->hasLoyaltyPointBalance(1)
        ->hasBusiness(1)
        ->create();
    }
    
}
