<?php

namespace App\Models;

use Auth;

use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Support\Facades\DB;

use App\Models\PlaceToEatItem;

use App\Models\ItemLedger;

use Carbon\Carbon;
use Dotenv\Loader\Loader;

class LoyaltyPointBalance extends Model
{
    use HasFactory, SoftDeletes;

    public $fillable = ['balance'];

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }  

    public function store(Request $request){

        $validatedData = $request->validate([
            'businessLoyaltyPoints' => ['numeric'],
            'businessCoinPercentage' => ['numeric']
        ]);

        $success = false;

        $user = Auth::user();

        if($user){
            
            $user->loyaltyPointBalance->reset_balance = $validatedData['businessLoyaltyPoints']; 

            if($user->loyaltyPointBalance->end_of_month === null){
                $user->loyaltyPointBalance->end_of_month = Carbon::now();
                $user->loyaltyPointBalance->balance = $validatedData['businessLoyaltyPoints']; 
            }
            
            $user->loyaltyPointBalance->loyalty_point_dollar_percent_value = $validatedData['businessCoinPercentage'];

            DB::transaction(function () use ($user){
                $user->loyaltyPointBalance->save();
            });  

            $success = true;

        } else
            $success = false;
            

        $response = array(
            'success' => $success
        );

        return response($response);        

    }

    public function show(){

        $success = false;

        $user = Auth::user();

        $loyaltyPoints = $user
        ->loyaltyPointBalance()
        ->select('balance', 'reset_balance', 'loyalty_point_dollar_percent_value', 'end_of_month')
        ->get()[0];

        if($loyaltyPoints)
            $success = true;
        else
            $success = false;

        $response = array(
            'success' => $success,
            'loyalty_points' => $loyaltyPoints
        );

        return response($response);
        
    }

    public function add(Request $request){
        
        $validatedData = $request->validate([
            'qr_code_link' => ['required', 'string'],
            'user_hash' => ['required', 'string'],
            'loyaltyPointReward' => ['required', 'numeric'],
            'totalSpent' => ['required', 'numeric']
        ]);

        $success = false;

        $user = Auth::user();

        $qrTokenHash = PlaceToEat::select('id')
        ->where('qr_code_link', $validatedData['qr_code_link'])        
        ->get()[0];

        $qrTokenOwner = User::select(['id', 'uuid'])
        ->where('uuid', $validatedData['user_hash'])
        ->get()[0];

        $loyaltyPointReward = $validatedData['loyaltyPointReward'];

        if( !is_null($qrTokenHash) && !is_null($qrTokenOwner) ){            
            
            $businessId = $qrTokenOwner->id;

            //Insert reward into ledger
            $insertReward = new ItemLedger();
            $insertReward->user_id = $user->id;
            $insertReward->loyalty_amount = abs( floatval($loyaltyPointReward) );

            //Insert expense into ledger
            $insertExpense = new ItemLedger();
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

        $response = array(
            'success' => $success,
            'newBalance' => $newUserBalance,
            'loyaltyPointReward' => $loyaltyPointReward
        );

        return response($response);

    }
}