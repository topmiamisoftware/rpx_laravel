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
        Business::factory()
        ->hasRewards(5)
        ->hasAds(4)
        ->count(10)
        ->create();        

        $businessList = Business::select('id')
        ->get();
        
        foreach ($businessList as $business) {            
            
            $userType = rand(1,2);

            DB::table('spotbie_users')
            ->where('user_type', 0)
            ->update([
                'user_type' => $userType
            ]);            
            
        }

    }
}
