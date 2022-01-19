<?php

namespace Database\Factories;

use App\Models\SpotbieAds;
use App\Models\Business;
use App\Models\User;
use App\Models\Models;
use App\Models\SpotbieUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

class SpotbieAdsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SpotbieAds::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->unique()->realText(25);

        $images = '';

        $adType = rand(0,2);

        return [
            'images' => $images,
            'images_mobile' => $images,
        ];
    }

    public function configure(){
        return $this->afterCreating(function (SpotbieAds $ad) {
            $ad->images = $this->getAdsPhoto('desktop', $ad->id);
            $ad->images_mobile = $this->getAdsPhoto('mobile', $ad->id);

            DB::transaction(function () use ($ad){
                $ad->save();
            });
        });
    }

    public function getAdsPhoto($adType, $adId){
        $businessPhoto = '';
        $businessPhoto = 'assets/images/def/spotbie/'. $adType . '/' . $adId;
        $businessPhoto = config('spotbie.spotbie_front_end_ip') . $businessPhoto . '.jpg';
        
        return $businessPhoto;
    } 
}