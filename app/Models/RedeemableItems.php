<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * @property mixed       $business_id
 * @property false|mixed $redeemed
 * @property mixed       $loyalty_point_dollar_percent_value
 * @property mixed       $dollar_value
 * @property mixed       $total_spent
 * @property mixed redeemer_id
 * @property mixed reward_id
 * @property mixed ledger_record_id
 * @property mixed                            $amount
 * @property mixed|\Ramsey\Uuid\UuidInterface $uuid
 */
class RedeemableItems extends Model
{
    use HasFactory;

    public function loyaltyPointLedger()
    {
        return $this->hasOne('App\Models\LoyaltyPointLedger', 'id', 'ledger_record_id');
    }

    public function reward()
    {
        return $this->hasOne('App\Models\Reward', 'id', 'reward_id');
    }

    public function receiptData()
    {
        return $this->hasOne('App\Models\ReceiptData', 'redeemable_id', 'id');
    }

    public function business()
    {
        return $this->hasOne('App\Models\Business', 'id', 'business_id');
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'amount'       => ['required', 'numeric'],
            'total_spent'  => ['required', 'numeric'],
            'dollar_value' => ['required', 'numeric'],
        ]);

        $user = Auth::user();

        if ($user)
        {
            if ($user->business->loyaltyPointBalance->balance < $validatedData['amount'])
            {
                $response = [
                    'success' => false,
                    'message' => "Business doesn't have enough Loyalty Points.",
                ];
                return response($response);
            }

            $redeemable = new RedeemableItems();
            $redeemable->business_id = $user->business->id;
            $redeemable->uuid = Str::uuid();
            $redeemable->amount = $validatedData['amount'];
            $redeemable->total_spent = $validatedData['total_spent'];
            $redeemable->dollar_value = $validatedData['dollar_value'];
            $redeemable->loyalty_point_dollar_percent_value = $user->business->loyaltyPointBalance->loyalty_point_dollar_percent_value;
            $redeemable->redeemed = false;

            DB::transaction(function () use ($redeemable) {
                $redeemable->save();
            }, 3);

            $redeemable->refresh();
        }
        else
        {
            $redeemable = null;
        }

        $response = [
            'success'    => true,
            'redeemable' => $redeemable,
        ];

        return response($response);
    }

    public function scanReceipt(Request $request)
    {
        $success = true;
        $message = null;

        $validatedData = $request->validate([
            'file' => 'required|image|max:25000',
        ]);

        $user = Auth::user();

        $hashedFileName = $validatedData['file']->hashName();
        // $receiptData = $validatedData['receiptData'];
        $environment = App::environment();

        $imagePath = 'receipts/images/' . $user->id . '/';

        if ($environment == 'local')
        {
            Storage::put($imagePath, $request->file('file'));
            $imagePath = config('app.url') . '/' . $imagePath;
        }
        else
        {
            Storage::put($imagePath, $request->file('file'), 'public');
            $imagePath = config('app.url') . '/' . $imagePath . $hashedFileName;
        }

        $redeemable = new RedeemableItems();
        $redeemable->business_id = 0;
        $redeemable->uuid = Str::uuid();
        $redeemable->amount = 250;
        $redeemable->total_spent = 0;
        $redeemable->dollar_value = 0;
        $redeemable->loyalty_point_dollar_percent_value = 1;
        $redeemable->redeemed = true;

        $insertLp = new LoyaltyPointLedger();
        $insertLp->user_id = $user->id;
        $insertLp->uuid = Str::uuid();
        $insertLp->business_id = 0;
        $insertLp->loyalty_amount = 250;
        $insertLp->type = 'points';

        $user->loyaltyPointBalanceAggregator->balance += $insertLp->loyalty_amount;
        $user->loyaltyPointBalanceAggregator->save();
        $user->loyaltyPointBalanceAggregator->refresh();

        DB::transaction(function () use ($redeemable, $insertLp, $user, $imagePath) {
            $redeemable->save();
            $insertLp->save();

            $insertLp->refresh();
            $redeemable->refresh();

            $redeemable->ledger_record_id = $insertLp->id;
            $redeemable->save();

            ReceiptData::create([
                'user_id' => $user->id,
                'image_path' => $imagePath,
                'redeemable_id' => $redeemable->id,
                'status' => 0,
                'data' => null,
            ]);
        }, 3);

        $response = [
            'success' => $success,
            'message' => $environment,
            'loyalty_points' => $user->loyaltyPointBalanceAggregator->balance,
            'award_points' => 250
        ];

        return response($response);
    }


    public function redeem(Request $request)
    {
        $validatedData = $request->validate([
            'redeemableHash' => ['required', 'string'],
        ]);

        $user = Auth::user();

        $redeemable = RedeemableItems::where('uuid', $validatedData['redeemableHash'])
        ->first();

        if ($redeemable)
        {
            if ($redeemable->redeemed === 1)
            {
                $response = [
                    'success' => false,
                    'message' => 'Points already redeemed.',
                ];
                return response($response);
            }

            $redeemable->redeemed = 1;
            $redeemable->redeemer_id = $user->id;

            // Add to ledger and to LP Balance
            // Insert reward into ledger
            $insertLp = new LoyaltyPointLedger();
            $insertLp->user_id = $user->id;
            $insertLp->uuid = Str::uuid();
            $insertLp->business_id = $redeemable->business->id;
            $insertLp->loyalty_amount = abs(floatval($redeemable->amount));
            $insertLp->type = 'points';

            // Insert expense into ledger
            $insertExpense = new LoyaltyPointLedger();
            $insertExpense->user_id = $user->id;
            $insertExpense->uuid = Str::uuid();
            $insertExpense->business_id = $redeemable->business->id;
            $insertExpense->loyalty_amount = (-abs(floatval($redeemable->amount)));
            $insertExpense->type = 'points_expense';

            // save these variables for later use.
            $expense = $insertExpense->loyalty_amount;
            $reward = $insertLp->loyalty_amount;

            // Reflect reward into personal user business balance.
            $userCurrentBalance = $user->loyaltyPointBalance()->where('from_business', $redeemable->business->id)->first();
            if (is_null($userCurrentBalance))
            {
                $lp = new LoyaltyPointBalance();
                $lp->user_id = $user->id;
                $lp->balance = 0;
                $lp->from_business = $redeemable->business_id;
                $lp->business_id = 0;
                DB::transaction(function () use ($lp) {
                    $lp->save();
                }, 3);
                $userCurrentBalance = $user->loyaltyPointBalance()->where('from_business', $redeemable->business->id)->first();
            }
            $newUserBalance = $userCurrentBalance->balance + $reward;

            // Reflect the expense into the business balance.
            $businessCurrentBalance = LoyaltyPointBalance::where('business_id', $redeemable->business_id)->first()->balance;
            $newBusinessBalance = $businessCurrentBalance + $expense;

            // Check if the business has enough LP to let the user Redeem
            if (($businessCurrentBalance - $expense) < 0)
            {
                $response = [
                    'success' => false,
                    'message' => "Business doesn't have enough Loyalty Points.",
                ];
                return response($response);
            }

            DB::transaction(function () use (
                $insertLp,
                $insertExpense,
                $user,
                $redeemable,
                $newUserBalance,
                $newBusinessBalance
            ) {
                $insertLp->save();
                $insertExpense->save();
                $redeemable->save();

                LoyaltyPointBalance::where('business_id', $redeemable->business_id)
                ->update([
                    'balance' => $newBusinessBalance,
                ]);

                $user->loyaltyPointBalanceAggregator->balance += $insertLp->loyalty_amount;
                $user->loyaltyPointBalanceAggregator->save();

                $user->loyaltyPointBalance()->where('from_business', $redeemable->business_id)->update([
                    'balance' => $newUserBalance,
                ]);
            }, 3);

            $redeemable->refresh();
            $user->loyaltyPointBalanceAggregator->refresh();

            $response = [
                'success'        => true,
                'redeemable'     => $redeemable,
                'loyalty_points' => $user->loyaltyPointBalanceAggregator->balance,
            ];

            return response($response);
        }
    }

    public function lpRedeemed(Request $request)
    {
        $user = Auth::user();
        /*
         * Personal account or business account.
         */
        if ($user->spotbieUser->user_type === 4)
        {
            $redeemedList = $user
                ->redeemed()
                ->with('loyaltyPointLedger')
                ->with('business', function ($query) {
                    $query->with('spotbieUser');
                })
                ->where('reward_id', '=', null)
                ->orderBy('redeemable_items.created_at', 'desc')
                ->paginate(10);
        }

        $response = [
            'success'      => true,
            'redeemedList' => $redeemedList,
        ];

        return response($response);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->spotbieUser->user_type === 4)
        {
            $rewardList = $user
                ->redeemed()
                ->with('loyaltyPointLedger')
                ->with('business', function ($query) {
                    $query->with('spotbieUser');
                })
                ->with('reward')
                ->where('reward_id', '!=', null)
                ->orderBy('redeemable_items.created_at', 'desc')
                ->paginate(10);
        }
        else
        {
            $rewardList = DB::table('redeemable_items')
                ->join('business', 'redeemable_items.business_id', '=', 'business.id')
                ->join('users', 'redeemable_items.business_id', '=', 'users.id')
                ->join('rewards', 'redeemable_items.reward_id', '=', 'rewards.id')
                ->select(
                    'redeemable_items.uuid',
                    'redeemable_items.redeemer_id',
                    'redeemable_items.amount',
                    'redeemable_items.total_spent',
                    'redeemable_items.dollar_value',
                    'redeemable_items.loyalty_point_dollar_percent_value',
                    'redeemable_items.redeemed',
                    'redeemable_items.updated_at',
                    'users.username',
                    'spotbie_users.default_picture',
                    'spotbie_users.user_type',
                    'rewards.name AS reward_name',
                    'rewards.images AS reward_image',
                    'rewards.point_cost AS point_cost'
                )
                ->where('redeemable_items.business_id', $user->business->id)
                ->orderBy('redeemable_items.id', 'desc')
                ->paginate(5);
        }

        $response = [
            'success'    => true,
            'rewardList' => $rewardList,
        ];

        return response($response);
    }
}
