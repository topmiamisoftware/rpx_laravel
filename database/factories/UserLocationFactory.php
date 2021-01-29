<?php

namespace Database\Factories;

use App\Models\UserLocation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserLocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserLocation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'loc_x' => "25.858726", 
            'loc_y' => "-80.302813", 
            'ip_address' => '0'
        ];
    }
}
