<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use App\Models\Models;
use App\Models\SpotbieUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;

class BusinessFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Business::class;

    private $alreadyAddedIds = array();

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $user = User::inRandomOrder()->first();

        $userId = $user->id;

        $userType = $user->spotbieUser->user_type;
        
        while(  in_array($userId, $this->alreadyAddedIds) ){
            $userId = User::inRandomOrder()->first()->id;
        }

        array_push($this->alreadyAddedIds, $userId);

        $name = $this->faker->unique()->realText(25);
        $description = $this->faker->unique()->realText(150);        

        $minX = floatval( config("spotbie.my_loc_x") ) - .06;
        $maxX = floatval( config("spotbie.my_loc_x") ) + .06;

        $minY = floatval( config("spotbie.my_loc_y") ) - .06;
        $maxY = floatval( config("spotbie.my_loc_y") ) + .06;

        $randomLocX = $this->faker->randomFloat(6, $minX, $maxX);

        $randomLocY = $this->faker->randomFloat(6, $minY, $maxY);

        $businessPhotoFolder = 'assets/images/def/places-to-eat/';

        if($userType == '1'){
            $businessPhotoFolder = 'assets/images/def/places-to-eat/';
        } else if($userType == '2'){
            $businessPhotoFolder = 'assets/images/def/events/';
        } else if($userType == '3'){
            $businessPhotoFolder = 'assets/images/def/shopping/';
        }

        $businessPhoto = config('spotbie.spotbie_front_end_ip') . $businessPhotoFolder . rand(1,25) . '.jpg';

        return [
            'id' => $userId,
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $description,
            'loc_x' => $randomLocX,
            'loc_y' => $randomLocY,
            'address' => config("spotbie.my_address"),
            'categories' => json_encode(config("spotbie.my_business_categories")),
            'is_verified' => true,
            'photo' => $businessPhoto,
            'qr_code_link' => Str::uuid()
        ];
        
    }


}
