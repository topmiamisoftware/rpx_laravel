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

        $numberOfBusinessUsers = 90;
        $numberOfBusinessTypes = 3;//Events, Retail, Places to Eat, etc.

        $businessesToCreate = $numberOfBusinessUsers / $numberOfBusinessTypes;

        $personalAccountsToCreate = 10;

        User::factory()
        ->count($personalAccountsToCreate)
        ->hasSpotbieUser(1, function (array $attributes){
            return [
                'user_type' => 4,
            ]; 
        })
        ->hasLoyaltyPointBalance(1)
        ->create();

        User::factory()
        ->count($businessesToCreate)
        ->hasSpotbieUser(1, function (array $attributes){
            return [
                'user_type' => 1,
            ];                        
        })
        ->hasBusiness(1)
        ->hasLoyaltyPointBalance(1)
        ->create();

        User::factory()
        ->count($businessesToCreate)
        ->hasSpotbieUser(1, function (array $attributes){
            return [
                'user_type' => 2,
            ];                        
        })
        ->hasBusiness(1)
        ->hasLoyaltyPointBalance(1)
        ->create();  
        
        User::factory()
        ->count($businessesToCreate)
        ->hasSpotbieUser(1, function (array $attributes){
            return [
                'user_type' => 3,
            ];                        
        })
        ->hasBusiness(1)
        ->hasLoyaltyPointBalance(1)
        ->create();  

        $placeToEatCategories = config('spotbie.my_business_categories_food');

        $indexOfBurgers = array_search('Burgers', $placeToEatCategories);

        $placeToEatCategory = json_encode(array($indexOfBurgers));

        //Let's create 8 Burger shops so that this category may always return full paged results.
        User::factory()
        ->count(8)
        ->hasSpotbieUser(1, function (array $attributes){
            return [
                'user_type' => 1,
            ];                        
        })
        ->hasBusiness(1, function (array $attributes) use ($placeToEatCategory){
            return [
                'categories' => $placeToEatCategory,
            ];                        
        })
        ->hasLoyaltyPointBalance(1)
        ->create();  

        /* Account #1 */
        User::factory()
        ->state([
            'email' => 'maindedeux@gmail.com',
            'username' => 'agent000',
            'password' => Hash::make('HelloWorld33!')
        ])
        ->count(1)
        ->hasSpotbieUser(1, function (array $attributes){
            return [
                'first_name' => 'Franco',
                'last_name' => 'Petitfour',
                'user_type' => 4
            ];                        
        })
        ->hasLoyaltyPointBalance(1)
        ->create();  

        /* Account #2 */
        User::factory()
        ->state([
            'email' => 'agent001@spotbie.com',
            'username' => 'agent001',
            'password' => Hash::make('HelloWorld33!')
        ])        
        ->count(1)
        ->hasSpotbieUser(1, function (array $attributes){
            return [
                'first_name' => 'Andres',
                'last_name' => 'Garcia',
                'user_type' => 4
            ];                        
        })
        ->hasLoyaltyPointBalance(1)
        ->create();  

    }
    
}
