<?php

namespace Database\Factories;

use App\Models\LoyaltyPointBalance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LoyaltyPointBalanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LoyaltyPointBalance::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "balance" => 0,
            "reset_balance" => 0,
            "end_of_month" => null,            
        ];
    }
}