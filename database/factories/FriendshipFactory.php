<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FriendshipFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Friendship::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        
        $friendshipPair = $this->generateUniqueFriendshipPair();

        return [
            'user_id' => $friendshipPair['user_id'],
            'peer_id' => $friendshipPair['peer_id'],
            'relation' => $this->faker->numberBetween(0,2)
        ];

    }

    public function generateUniqueFriendshipPair(){

        $user_id = User::find(1)->id;
        $peer_id = User::all()->random()->id;
        
        while($peer_id == $user_id){
            $peer_id = User::all()->random()->id;
        }
        
        $realtionship_exists = Friendship::select('user_id')
        ->where('user_id', $user_id)
        ->where('peer_id', $peer_id)
        ->get();
        
        if(count($realtionship_exists) > 0)
            return $this->generateUniqueFriendshipPair();
        
        return array(
            'user_id' => $user_id,
            'peer_id' => $peer_id
        );
        
    }

}