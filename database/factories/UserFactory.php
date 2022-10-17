<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\LoyaltyPointBalance;
use App\Models\RedeemableItems;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => Str::uuid(),
            'username' => $this->faker->unique()->username,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('HelloWorld33!')
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            if(!$user->business){
                // Let's attach some Loyalty Point Balances from different stores.
                for($i = 0; $i < 10; $i++) {
                    // We seed DB with 90 business accounts;
                    $randBusinessId = rand(12, 122);

                    while( in_array($randBusinessId, $user->loyaltyPointBalance()->pluck('from_business')->toArray()) ){
                        $randBusinessId = rand(12, 122);
                    }

                    $user->loyaltyPointBalance()->create([
                        'balance' => rand(1000, 2000),
                        'from_business' => $randBusinessId,
                        'business_id' => 0,
                    ]);
                }
            }
        });
    }
}
