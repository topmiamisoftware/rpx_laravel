<?php

namespace App\Models;

use Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\LoyaltyPointLedger;
use App\Models\LoyaltyPointBalance;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class RedeemableLoyaltyPoint extends Model
{
    
    use HasFactory;

    public function create(Request $request){

        $validatedData = $request->validate([
            'amount' => ['required', 'numeric'],
            'total_spent' => ['required', 'numeric'],
            'dollar_value' => ['required', 'numeric'],
        ]);

        $user = Auth::user();

        if($user){

            if($user->loyaltyPointBalance->balance < $validatedData['amount']){

                $response = array(
                    "success" => false,
                    "message" => "Business doesn't have enough Loyalty Points."
                );
        
                return response($response);

            }

            $lpToDollarRatio = $user->loyaltyPointBalance->loyalty_point_dollar_percent_value;

            $redeemable = new RedeemableLoyaltyPoint();
            $redeemable->user_id = $user->id;
            $redeemable->uuid = Str::uuid();
            $redeemable->amount  = $validatedData['amount'];
            $redeemable->total_spent  = $validatedData['total_spent'];
            $redeemable->dollar_value  = $validatedData['dollar_value'];
            $redeemable->loyalty_point_dollar_percent_value = $lpToDollarRatio;
            $redeemable->redeemed = false;
            
            DB::transaction(function () use ($redeemable) {
                $redeemable->save();
            });

        }   

        $redeemable->refresh();

        $response = array(
            "success" => true,
            "redeemable" => $redeemable
        );

        return response($response);

    }

    public function redeem(Request $request){

        $validatedData = $request->validate([
            'redeemableHash' => ['required', 'string']
        ]);

        $user = Auth::user();

        $redeemable = RedeemableLoyaltyPoint::select()
        ->where('uuid', $validatedData['redeemableHash'])
        ->first();

        if($redeemable){

            if($redeemable->redeemed === 1){

                $response = array(
                    "success" => false,
                    "message" => 'Points already redeemed.'
                );
        
                return response($response);

            }

            $redeemable->redeemed = 1;
            $redeemable->redeemer_id = $user->id;

            //Add to ledger and to LP Balance 
            //Insert reward into ledger
            $insertLp = new LoyaltyPointLedger();
            $insertLp->user_id = $user->id;
            $insertLp->loyalty_amount = abs( floatval($redeemable->amount) );

            //Insert expense into ledger
            $insertExpense = new LoyaltyPointLedger();
            $insertExpense->user_id = $redeemable->user_id;
            $insertExpense->loyalty_amount = ( - abs(floatval($redeemable->amount)) );

            //save these variabels for later use.
            $expense = $insertExpense->loyalty_amount;
            $reward = $insertLp->loyalty_amount;

            //Reflect reward into personal user's balance.
            $userCurrentBalance = $user->loyaltyPointBalance->balance;
            $newUserBalance = $userCurrentBalance + $reward;

            //Reflect the expense into the business balance.
            $businessCurrentBalance = LoyaltyPointBalance::find( $redeemable->user_id )->balance;
            $newBusinessBalance = $businessCurrentBalance + $expense;                                                         
            
            //Check if the business has enough LP to let the user Redeem
            if( ($businessCurrentBalance - $expense) <= 0 ){

                $response = array(
                    "success" => false,
                    "message" => "Business doesn't have enough Loyalty Points."
                );
        
                return response($response);
            }

            DB::transaction(function () use (
                    $insertLp, $insertExpense, 
                    $user, $redeemable, $newUserBalance, 
                    $newBusinessBalance) 
            {
                
                $insertLp->save();
                $insertExpense->save();                
                $redeemable->save();
                
                $balanceRemove = LoyaltyPointBalance::find($redeemable->user_id)
                ->update([
                    "balance" => $newBusinessBalance
                ]);
                
                $balanceAdd = $user->loyaltyPointBalance()
                ->update([
                    "balance" => $newUserBalance
                ]);

            });

            $success = true;

            $redeemable->refresh();

            $loyaltyPoints = $user->loyaltyPointBalance()
            ->select('balance', 'reset_balance', 'loyalty_point_dollar_percent_value', 'end_of_month')
            ->get()[0];

            $response = array(
                "success" => true,
                "redeemable" => $redeemable,
                "loyalty_points" => $loyaltyPoints
            );
    
            return response($response);

        }



    }

    public function index(){

        $user = Auth::user();

        if($user->spotbieUser->user_type == 4){

            $redeemedList = $user->redeemed()
            ->join('spotbie_users', 'redeemable_loyalty_points.user_id', '=', 'spotbie_users.id')
            ->join('users', 'redeemable_loyalty_points.user_id', '=', 'users.id')
            ->join('business', 'redeemable_loyalty_points.user_id', '=', 'business.id')
            ->select(
                'redeemable_loyalty_points.uuid', 'redeemable_loyalty_points.user_id', 'redeemable_loyalty_points.amount', 
                'redeemable_loyalty_points.total_spent', 'redeemable_loyalty_points.dollar_value', 'redeemable_loyalty_points.loyalty_point_dollar_percent_value', 
                'redeemable_loyalty_points.redeemed', 'redeemable_loyalty_points.updated_at',                
                'spotbie_users.default_picture', 'spotbie_users.user_type',
                'users.username',
                'business.name', 'business.address'    
            )       
            ->orderBy('redeemable_loyalty_points.id', 'desc')     
            ->paginate(5);

        } else {

            $redeemedList = DB::table('redeemable_loyalty_points')
            ->join('spotbie_users', 'redeemable_loyalty_points.user_id', '=', 'spotbie_users.id')
            ->join('users', 'redeemable_loyalty_points.user_id', '=', 'users.id')
            ->select(
                'redeemable_loyalty_points.uuid', 'redeemable_loyalty_points.redeemer_id', 'redeemable_loyalty_points.amount', 
                'redeemable_loyalty_points.total_spent', 'redeemable_loyalty_points.dollar_value', 'redeemable_loyalty_points.loyalty_point_dollar_percent_value', 
                'redeemable_loyalty_points.redeemed', 'redeemable_loyalty_points.updated_at',
                'users.username',
                'spotbie_users.default_picture', 'spotbie_users.user_type'    
            )
            ->where('redeemable_loyalty_points.user_id', $user->id)
            ->orderBy('redeemable_loyalty_points.id', 'desc')
            ->paginate(5);

        }

        $response = array(
            "success" => true,
            "redeemedList" => $redeemedList
        );

        return response($response);

    }

}
