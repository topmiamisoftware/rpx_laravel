<?php

namespace Database\Seeders;

use Database\Factories\BusinessFactory;

use Illuminate\Database\Seeder;
use App\Models\Business;

use Illuminate\Support\Facades\DB;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::transaction(function(){

            Business::factory()
            ->hasRewards(5)
            ->hasAds(4)
            ->count(10)
            ->create(); 
           
        });

    }
}
