<?php

namespace Database\Factories;

use App\Models\WebOptions;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WebOptionsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WebOptions::class;

    protected $color_array = array(
        '#5f882f',
        '#252982',
        '#0d0f38',
        '#902375',
        '#e170c5',
        '#18073c',
        '#131313',
        '#247376',
        '#56dc3c',
        '#cddc3c',
        '#dc753c',
        '#8e3312'
    );

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'bg_color' => $this->faker->randomElement($this->color_array)
        ];
    }

}
