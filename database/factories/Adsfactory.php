<?php

namespace Database\Factories;

use App\Models\Ads;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
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

        $images = '';

        $adType = rand(0, 2);

        return [
            'uuid'          => Str::uuid(),
            'type'          => $adType,
            'name'          => $name,
            'images'        => $images,
            'images_mobile' => $images,
            'dollar_cost'   => 0,
            'clicks'        => rand(0, 500),
            'views'         => rand(0, 1500),
            'is_live'       => 1,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Ads $ad) {
            $spotbieUserType = $ad->business->user->spotbieUser->user_type;

            $adImage = $this->getAdsPhoto($spotbieUserType);

            $ad->images = $adImage;
            $ad->images_mobile = $adImage;

            DB::transaction(function () use ($ad) {
                $ad->save();
            });
        });
    }

    public function getAdsPhoto($userType)
    {
        $businessPhotoFolder = 'assets/images/in-house/places-to-eat/';

        switch($userType)
        {
            case '1':
                $businessPhotoFolder = 'assets/images/in-house/places-to-eat/';
                break;
            case '2':
                $businessPhotoFolder = 'assets/images/in-house/shopping/';
                break;
            case '3':
                $businessPhotoFolder = 'assets/images/in-house/events/';
                break;
        }

        $businessPhoto = config('spotbie.spotbie_front_end_ip') . $businessPhotoFolder . rand(1, 25) . '.jpg';

        return $businessPhoto;
    }
}
