<?php

use App\Models\Business;
use App\Models\SpotbieUser;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

use Laracasts\TestDummy\Factory as TestDummy;

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
