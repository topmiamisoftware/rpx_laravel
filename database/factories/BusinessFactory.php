<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;

use App\Models\Business;
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
            'categories' => json_encode(config("spotbie.my_business_categories")),
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

        });

    }

}
