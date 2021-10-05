<?php

namespace Database\Factories;

use App\Models\PlaceToEatItem;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlaceToEatItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlaceToEatItem::class;

    public $rewardImageList = array(
        "https://localhost:4200/assets/images/def/rewards/sample_image_0.jpg",
        "https://localhost:4200/assets/images/def/rewards/sample_image_1.jpg",
        "https://localhost:4200/assets/images/def/rewards/sample_image_2.jpg",
        "https://localhost:4200/assets/images/def/rewards/sample_image_3.jpg",
        "https://localhost:4200/assets/images/def/rewards/sample_image_4.jpg"
    );

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $name = $this->faker->unique()->realText(50);
        $description = $this->faker->unique()->realText(150);

        $images = $this->rewardImageList[rand(0,4)];

        $point_cost = rand(1, 500);

        $monthly_times_available = rand(1, 200);
        $times_claimed_this_month = rand(1, 200);

        $type = rand(0, 1);

        return [
            'type' => $type,
            'name' => $name,
            'description' => $description,
            'images' => $images,
            'point_cost' => $point_cost,
            'monthly_times_available' => $monthly_times_available,
            'times_claimed_this_month' => $times_claimed_this_month           
        ];
    }
}
