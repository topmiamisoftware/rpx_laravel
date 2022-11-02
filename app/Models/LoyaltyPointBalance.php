<?php

namespace App\Models;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoyaltyPointBalance extends Model
{
    use SoftDeletes;

    public $table = "loyalty_point_balances";

    public $fillable = ['balance', 'loyalty_point_dollar_percent_value', 'end_of_month'];

    public function user(){
        return $this->belongsTo('App\Models\User', 'id', 'id');
    }

    public function fromBusiness(){
        return $this->belongsTo('App\Models\Business', 'from_business', 'id');
    }

    public function balanceList(){
        $user = Auth::user();

        if($user->spotbieUser->user_type === 4){
            $balanceList = $user->loyaltyPointBalance()
                ->whereHas('fromBusiness')
                ->with('fromBusiness', function ($query) {
                    $query->with('spotbieUser');
                })
                ->orderBy('updated_at', 'desc')
                ->paginate(10);
        } else {
            // User is a business
            $balanceList = $user->business()->loyaltyPointBalance()->get();
        }

        $response = array(
            'success' => true,
            'balanceList' => $balanceList
        );

        return response($response);
    }

    public function store(Request $request){
        $validatedData = $request->validate([
            'businessLoyaltyPoints' => ['required', 'numeric'],
            'businessCoinPercentage' => ['required', 'numeric']
        ]);

        $success = false;
        $user = Auth::user();
        $loyaltyPointBalance = null;

        if($user){
            $loyaltyPointBalance = $user->business->loyaltyPointBalance;
            $reset_balance = doubleval($validatedData['businessLoyaltyPoints']);
            $balance = $reset_balance;
            $end_of_month = Carbon::now();

            $loyalty_point_dollar_percent_value = $validatedData['businessCoinPercentage'];

            $loyaltyPointBalance->balance = $balance;
            $loyaltyPointBalance->reset_balance = $reset_balance;
            $loyaltyPointBalance->end_of_month = $end_of_month;
            $loyaltyPointBalance->loyalty_point_dollar_percent_value = $loyalty_point_dollar_percent_value;

            $loyaltyPointBalance->save();

            $success = true;
            $loyaltyPointBalance = $loyaltyPointBalance->refresh();
        }

        $response = array(
            'success' => $success,
            'lp_balance' => $loyaltyPointBalance
        );

        return response($response);
    }

    public function show() {
        $success = false;
        $user = Auth::user();

        if ($user->business){
            $loyaltyPoints = $user
                ->business
                ->loyaltyPointBalance()
                ->select('balance', 'reset_balance', 'loyalty_point_dollar_percent_value', 'end_of_month')
                ->get();
        } else {
            $loyaltyPoints = $user
                ->loyaltyPointBalance()
                ->select('balance', 'reset_balance', 'loyalty_point_dollar_percent_value', 'end_of_month')
                ->get();
        }

        if( $loyaltyPoints ){
            $success = true;
        } else {
            $loyaltyPoints = new LoyaltyPointBalance();
            $loyaltyPoints->balance = 0;
            $loyaltyPoints->reset_balance = 0;
            $loyaltyPoints->loyalty_point_dollar_percent_value = 0;
            $loyaltyPoints->end_of_month = null;
        }

        $response = array(
            'success' => $success,
            'loyalty_points' => $loyaltyPoints
        );

        return response($response);
    }

    public function reset(){
        $user = Auth::user();

        if($user){
            $user->business->loyaltyPointBalance->balance = $user->business->loyaltyPointBalance->reset_balance;

            DB::transaction(function () use ($user){
                $user->business->loyaltyPointBalance->save();
            }, 3);

            $loyaltyPointBalance = $user->business->loyaltyPointBalance()
            ->select('balance', 'reset_balance', 'loyalty_point_dollar_percent_value', 'end_of_month')
            ->get()[0];

            $success = true;
        } else {
            $loyaltyPointBalance = 0;
            $success = false;
        }

        $response = array(
            'success' => $success,
            'loyalty_points' => $loyaltyPointBalance
        );

        return response($response);
    }
}
