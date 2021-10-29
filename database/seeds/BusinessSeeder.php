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

        if($createBusiness){
         
            $businessList = Business::select('id')
            ->get();
            
            error_log("total business: " . count($businessList) );
    
            foreach ($businessList as $business) {            
                
                error_log("business id" . $business->id );
    
                $userType = rand(1,2);
    
                SpotbieUser::where('id', $business->id)
                ->update([
                    'user_type' => $userType
                ]);            
                
            }

        }

    }
}
