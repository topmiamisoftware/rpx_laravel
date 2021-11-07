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

        $name = $this->faker->unique()->realText(25);
        $description = $this->faker->unique()->realText(150);

        $images = '';

        $adType = rand(0,2);

        switch($adType){
            case 0:
                $dollar_cost = 15.99;
                break;
            case 1:
                $dollar_cost = 13.99;
                break;
            case 2:
                $dollar_cost = 10.99;
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
            'is_live' => 1
        ];
        
    }

    public function configure(){

        return $this->afterCreating(function (Ads $ad) {
            
            $spotbieUserType = $ad->spotbieUser->user_type;

            $adImage = $this->getAdsPhoto($spotbieUserType);

            $ad->images = $adImage;

            DB::transaction(function () use ($ad){
                $ad->save();
            });

        });

    }

    public function getAdsPhoto($userType){

        $businessPhotoFolder = 'assets/images/def/places-to-eat/';

        switch($userType){
            case '1':
                $businessPhotoFolder = 'assets/images/def/places-to-eat/';
                break;
            case '2':
                $businessPhotoFolder = 'assets/images/def/shopping/';
                break;
            case '3':
                $businessPhotoFolder = 'assets/images/def/events/';
                break;   
        }

        $businessPhoto = config('spotbie.spotbie_front_end_ip') . $businessPhotoFolder . rand(1,25) . '.jpg';

        return $businessPhoto;

    }

}
