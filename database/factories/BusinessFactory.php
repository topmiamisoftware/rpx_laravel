<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;

use App\Models\Ads;
use App\Models\Business;
use App\Models\Reward;
use App\Models\SpotbieUser;


class BusinessFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Business::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $businessUserTypeList = [1, 2];
        
        $name = $this->faker->unique()->realText(25);
        $description = $this->faker->unique()->realText(150);        

        $minX = floatval( config("spotbie.my_loc_x") ) - .06;
        $maxX = floatval( config("spotbie.my_loc_x") ) + .06;

        $minY = floatval( config("spotbie.my_loc_y") ) - .06;
        $maxY = floatval( config("spotbie.my_loc_y") ) + .06;

        $randomLocX = $this->faker->randomFloat(6, $minX, $maxX);

        $randomLocY = $this->faker->randomFloat(6, $minY, $maxY);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $description,
            'loc_x' => $randomLocX,
            'loc_y' => $randomLocY,
            'address' => config("spotbie.my_address"),
            'categories' => json_encode(config("spotbie.my_business_categories")),
            'photo' => '',
            'is_verified' => true,        
            'qr_code_link' => Str::uuid()
        ];
        
    }

    public function configure(){

        return $this->afterCreating(function (Business $business) {
            
            $userType = rand(1,2);

            //Let's update the userType
            SpotbieUser::where('id', '=', $business->id)
            ->update([
                'user_type' => $userType
            ]);

            $business->photo = $this->getBusinessPhoto($userType);
            
            $business->save();

            Reward::factory()
            ->count(7)
            ->for($business)
            ->create();

            Ads::factory()
            ->count(3)
            ->for($business)
            ->create();

        });

    }

    public function getBusinessPhoto($userType){

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
