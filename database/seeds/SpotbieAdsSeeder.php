<?php

namespace Database\Seeders;

use App\Models\SpotbieAds;
use Illuminate\Database\Seeder;

class SpotbieAdsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SpotbieAds::factory()
        ->state([
            'type' => 0,
        ])
        ->count(1)
        ->create();

        SpotbieAds::factory()
        ->state([
            'type' => 1,
        ])
        ->count(1)
        ->create();

        SpotbieAds::factory()
        ->state([
            'type' => 2,
        ])
        ->count(1)
        ->create();

        SpotbieAds::factory()
        ->state([
            'type' => 3,
        ])
        ->count(1)
        ->create();

        SpotbieAds::factory()
        ->state([
            'type' => 4,
        ])
        ->count(1)
        ->create();
    }
}
