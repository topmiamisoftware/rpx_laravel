<?php

use App\Models\Business;
use App\Models\SpotbieUser;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class BusinessSeeder extends Seeder
{
    public function run()
    {
        Business::factory()
        ->hasRewards(5)
        ->hasAds(4)
        ->count(10)
        ->create();        

        $businessList = Business::select('id')
        ->get();
        
        foreach ($businessList as $business) {            
            
            DB::table('spotbie_users')
            ->where('id', $business->id)
            ->update([
                'user_type' => rand(1,2)
            ]);            

        }

    }
}
