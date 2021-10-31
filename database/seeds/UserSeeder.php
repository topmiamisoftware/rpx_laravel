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
                'user_type' => 1,
            ];                        
        })
        ->hasBusiness(1)
        ->hasLoyaltyPointBalance(1)
        ->create();

        User::factory()
        ->count(10)
        ->hasSpotbieUser(1, function (array $attributes){
            return [
                'user_type' => 2,
            ];                        
        })
        ->hasBusiness(1)
        ->hasLoyaltyPointBalance(1)
        ->create();  
        
        User::factory()
        ->count(10)
        ->hasSpotbieUser(1, function (array $attributes){
            return [
                'user_type' => 3,
            ];                        
        })
        ->hasBusiness(1)
        ->hasLoyaltyPointBalance(1)
        ->create();  

        /* Account #1 */
        User::factory(
            function (array $attributes){
                return [
                    'email' => 'agent000@spotbie.com',
                    'username' => 'agent000',
                    'password' => 'HelloWorld33!'
                ];                        
            }            
        )
        ->count(10)
        ->hasSpotbieUser(1, function (array $attributes){
            return [
                'first_name' => 'Franco',
                'last_name' => 'Petitfour',
                'user_type' => 4
            ];                        
        })
        ->hasBusiness(1)
        ->hasLoyaltyPointBalance(1)
        ->create();  

        /* Account #2 */
        User::factory(
            function (array $attributes){
                return [
                    'email' => 'agent001@spotbie.com',
                    'username' => 'agent001',
                    'password' => 'HelloWorld33!'
                ];                        
            }
        )
        ->count(10)
        ->hasSpotbieUser(1, function (array $attributes){
            return [
                'first_name' => 'Andres',
                'last_name' => 'Garcia',
                'user_type' => 4
            ];                        
        })
        ->hasBusiness(1)
        ->hasLoyaltyPointBalance(1)
        ->create();  

    }
    
}
