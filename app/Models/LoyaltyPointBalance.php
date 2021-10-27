<?php

namespace App\Models;

use Auth;

use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\DB;

use App\Models\Reward;
use App\Models\Business;
use App\Models\LoyaltyPointLedger;

use Carbon\Carbon;
use Dotenv\Loader\Loader;

class LoyaltyPointBalance extends Model
{
    use HasFactory, SoftDeletes;

    public $table = "loyalty_point_balances";

    public $fillable = ['balance', 'loyalty_point_dollar_percent_value', 'end_of_month'];

    public function user(){
        return $this->belongsTo('App\Models\User', 'id');
    }  

    public function store(Request $request){

        $validatedData = $request->validate([
            'businessLoyaltyPoints' => ['required', 'numeric'],
            'businessCoinPercentage' => ['required', 'numeric']
        ]);

        $success = false;
        
        $user = Auth::user();

        if($user){
            
            $loyaltyPointBalance = $user
            ->loyaltyPointBalance;   

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

        } else
            $success = false;
            
        $response = array(
            'success' => $success,
            'lp_balance' => $loyaltyPointBalance
        );

        return response($response);        

    }

    public function show(){

        $success = false;

        $user = Auth::user();
        
        $loyaltyPoints = $user
        ->loyaltyPointBalance()
        ->select('balance', 'reset_balance', 'loyalty_point_dollar_percent_value', 'end_of_month')
        ->get();

        if( $loyaltyPoints ){

            $success = true;
            $loyaltyPoints = $loyaltyPoints[0];

        } else {

            $success = false;
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

    public function add(Request $request){
        
        $validatedData = $request->validate([
            'qr_code_link' => ['required', 'string'],
            'loyaltyPointReward' => ['required', 'numeric'],
            'totalSpent' => ['required', 'numeric']
        ]);

        $success = false;

        $user = Auth::user();

        $qrTokenHash = Business::select('id')
        ->where('qr_code_link', $validatedData['qr_code_link'])        
        ->get()[0];

        $loyaltyPointReward = $validatedData['loyaltyPointReward'];
        https://spotbie.com/events/community/de05ee0c-8c43-40b9-9ff8-93d4378ebf65
        if( !is_null($qrTokenHash) ){            
            
            $businessId = $qrTokenHash->id;

            //Insert reward into ledger
            $insertReward = new LoyaltyPointLedger();
            $insertReward->user_id = $user->id;
            $insertReward->loyalty_amount = abs( floatval($loyaltyPointReward) );

            //Insert expense into ledger
            $insertExpense = new LoyaltyPointLedger();
            $insertExpense->user_id = $businessId;
            $insertExpense->loyalty_amount = ( - abs(floatval($loyaltyPointReward)) );

            //save these variabels for later use.
            $expense = $insertExpense->loyalty_amount;
            $reward = $insertReward->loyalty_amount;

            //Reflect reward into personal user's balance.
            $userCurrentBalance = $user->loyaltyPointBalance->balance;
            $newUserBalance = $userCurrentBalance + $reward;

            //Reflect the expense into the business balance.
            $businessCurrentBalance = LoyaltyPointBalance::find( $businessId )->balance;
            $newBusinessBalance = $businessCurrentBalance + $expense;                                                         
            
            DB::transaction(function () use (
                    $insertReward, $insertExpense, 
                    $user, $businessId, $newUserBalance, 
                    $newBusinessBalance) 
            {
                
                $insertReward->save();
                $insertExpense->save();
                    
                $balanceRemove = LoyaltyPointBalance::find($businessId)
                ->update([
                    "balance" => $newBusinessBalance
                ]);
                
                $balanceAdd = $user->loyaltyPointBalance()
                ->update([
                    "balance" => $newUserBalance
                ]);

            });

            $success = true;

        } else {
        
            $success = false;

        }

        $loyaltyPointBalance = $user->loyaltyPointBalance()
        ->select('balance', 'reset_balance', 'loyalty_point_dollar_percent_value', 'end_of_month')
        ->get()[0];

        $response = array(
            'success' => $success,
            'loyalty_points' => $loyaltyPointBalance,
            'loyaltyPointReward' => $loyaltyPointReward
        );

        return response($response);

    }

    public function reset(){

        $user = Auth::user();
        $success = false;

        $newUserBalance = 0;

        if($user){

            $user->loyaltyPointBalance->balance = $user->loyaltyPointBalance->reset_balance;

            DB::transaction(function () use ($user){
                $user->loyaltyPointBalance->save();
            });  
            
            $loyaltyPointBalance = $user->loyaltyPointBalance()
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