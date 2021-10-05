<?php

use App\Models\Business;
use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class BusinessSeeder extends Seeder
{
    public function run()
    {
        Business::factory()
        ->hasRewards(5)
        ->count(5)
        ->create();        
    }
}
