<?php

namespace Database\Seeders;

use App\Models\Business;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        User::factory()
        ->count(10)
        ->hasSpotbieUser(1, function (array $attributes){
            return [
                'user_type' => rand(1,2),
            ];                        
        })
        ->hasBusiness(1)
        ->hasLoyaltyPointBalance(1)
        ->create();
    }
    
}
