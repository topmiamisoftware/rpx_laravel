<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $createBusiness = Business::factory()
        ->hasRewards(5)
        ->hasAds(4)
        ->count(10)
        ->create(); 
    }
}
