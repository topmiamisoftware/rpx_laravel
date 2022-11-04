<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyPointBalanceAggregator extends Model
{
    use SoftDeletes;

    public $table = "loyalty_point_balance_aggregator";

    public $fillable = ['balance'];

    public function user(){
        return $this->belongsTo('App\Models\User', 'id', 'id');
    }

    public function show(){
        $user = Auth::user();

        if(!$user->business){
            $loyaltyPointBalanceAggregate = $user->loyaltyPointBalanceAggregator->balance;

            $response = array(
                'success' => true,
                'loyalty_points' => $loyaltyPointBalanceAggregate
            );
        } else {
            $loyaltyPointBalance = $user->business->loyaltyPointBalance()->get()[0];

            $response = array(
                'success' => true,
                'loyalty_points' => $loyaltyPointBalance
            );
        }

        return response($response);
    }

    public function updateAggregate(int $lp){
        $user = Auth::user();

        $user->loyaltyPointBalanceAggregator()->balance += $lp;
        $user->loyaltyPointBalanceAggregator()->save();
    }
}
