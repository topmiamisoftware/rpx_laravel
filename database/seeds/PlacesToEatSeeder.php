<?php

use App\Models\PlaceToEat;
use Illuminate\Database\Seeder;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class PlaceToEatSeeder extends Seeder
{
    public function run()
    {
        PlaceToEat::factory()
        ->hasPlaceToEatItems(5)
        ->count(5)
        ->create();        
    }
}
