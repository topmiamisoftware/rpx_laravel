<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\User;
use App\Models\Models;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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

        $userId = User::inRandomOrder()->first()->id;

        while(  in_array($userId, $this->alreadyAddedIds) ){
            $userId = User::inRandomOrder()->first()->id;
        }

        array_push($this->alreadyAddedIds, $userId);

        $name = $this->faker->unique()->realText(25);
        $description = $this->faker->unique()->realText(150);

        return [
            'user_id' => $userId,
            'name' => $name,
            'description' => $description,
            'loc_x' => config("spotbie.my_loc_x"),
            'loc_y' => config("spotbie.my_loc_y"),
            'address' => config("spotbie.my_address"),
            'categories' => json_encode(config("spotbie.my_business_categories")),
            'is_verified' => true,
            'qr_code_link' => Str::uuid()
        ];
        
    }
}
