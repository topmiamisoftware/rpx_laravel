<?php

use App\Models\Business;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    public function run()
    {
        $createBusiness = Business::factory()
        ->hasRewards(5)
        ->hasAds(4)
        ->count(10)
        ->create();       
    }
}
