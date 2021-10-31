<?php

namespace Database\Factories;

use App\Models\Ads;
use App\Models\Business;
use App\Models\User;
use App\Models\Models;
use App\Models\SpotbieUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

class AdsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ads::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $randomImage = config('spotbie.spotbie_front_end_ip') . 'assets/images/def/ads/' . rand(1,9) . '.jpg';

        $name = $this->faker->unique()->realText(25);
        $description = $this->faker->unique()->realText(150);

        $images = $randomImage;

        $adType = rand(0,2);

        switch($adType){
            case 0:
                $dollar_cost = 15.99;
                break;
            case 1:
                $dollar_cost = 13.99;
                break;
            case 2:
                $dollar_cost = 6.99;
                break;                                
        }

        return [
            'uuid' => Str::uuid(),
            'type' => $adType,
            'name' => $name,
            'description' => $description,
            'images' => $images,
            'dollar_cost' => $dollar_cost,
            'clicks' => rand(0,500),
            'views' => rand(0,1500),
            'is_subscription' => 1,
            'failed_subscription' => 1,
            'ends_at' => Carbon::now()->addHour(),
        ];
        
    }

    public function configure()
    {
        return $this->afterCreating(function (Ads $ad) {
            
            $randomBusinessId = DB::table('business')
            ->inRandomOrder()
            ->get()[0]->id;

            $ad->business_id = $randomBusinessId;

            $ad->save();

        });
    }

}
