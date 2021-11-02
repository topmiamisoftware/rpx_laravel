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
            'photo' => '',
            'is_verified' => true,        
            'qr_code_link' => Str::uuid()
        ];
        
    }

    public function configure(){

        return $this->afterCreating(function (Business $business) {

            $spotbieUser = SpotbieUser::select('user_type')
            ->where('id', '=', $business->id)
            ->get()[0];

            $userType = $spotbieUser->user_type;

            $business->photo = $this->getBusinessPhoto($userType);

            if($business->categories == null){

                $categories = '';
    
                switch($userType){
                    case '1':
                        $categories = array_rand(config("spotbie.my_business_categories_food"), 3);
                        break;
                    case '2':
                        $categories = array_rand(config("spotbie.my_business_categories_shopping"), 3);
                        break; 
                    case '3':
                        $categories = array_rand(config("spotbie.my_business_categories_events"), 3);
                }
    
                $business->categories = json_encode($categories);                

            }

            $business->save();

            Reward::factory()
            ->count(7)
            ->for($business)
            ->create();

            //Let's make 3 ads of each type for the business.
            Ads::factory()
            ->state([
                "type" => 0
            ])
            ->count(1)
            ->for($business)
            ->create();

            Ads::factory()
            ->state([
                "type" => 1
            ])            
            ->count(1)
            ->for($business)
            ->create();

            Ads::factory()
            ->state([
                "type" => 2
            ])            
            ->count(1)
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
