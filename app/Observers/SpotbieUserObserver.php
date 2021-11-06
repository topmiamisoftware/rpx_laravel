<?php

namespace App\Observers;

use App\Models\SpotbieUser;

use Carbon\Carbon;
use Laravel\Cashier\Cashier;

use Illuminate\Support\Facades\App;

class SpotbieUserObserver
{
    /**
     * Handle the SpotbieUser "created" event.
     *
     * @param  \App\Models\SpotbieUser  $spotbieUser
     * @return void
     */
    public function created(SpotbieUser $spotbieUser)
    {

        $user = $spotbieUser->user;

        if($user->stripe_id == null && $spotbieUser->user_type != '4'){
            
            //Create the user in stripe, and Laravel Cashier will also update all requried records on our database.
            $user->createAsStripeCustomer();
            
            $user->trial_ends_at = Carbon::now()->addMonths(3);

            DB::transaction(function () use ($user){
                $user->save();
            }, 3);

            $userBillable = Cashier::findBillable($user->stripe_id);

            $price = config('spotbie.business_subscription_product');
            $product = config('spotbie.business_subscription_price');

            //If the user is a business account, subscribe them to a free 30 day trial.
            if($user->spotbieUser->user_type !== '4'){

                $userBillable->newSubscription($adSubscription->id, [$price_name] )->create($paymentMethodId);
                
            }

        }
    }

    /**
     * Handle the SpotbieUser "updated" event.
     *
     * @param  \App\Models\SpotbieUser  $spotbieUser
     * @return void
     */
    public function updated(SpotbieUser $spotbieUser)
    {
        //
    }

    /**
     * Handle the SpotbieUser "deleted" event.
     *
     * @param  \App\Models\SpotbieUser  $spotbieUser
     * @return void
     */
    public function deleted(SpotbieUser $spotbieUser)
    {
        //
    }

    /**
     * Handle the SpotbieUser "restored" event.
     *
     * @param  \App\Models\SpotbieUser  $spotbieUser
     * @return void
     */
    public function restored(SpotbieUser $spotbieUser)
    {
        //
    }

    /**
     * Handle the SpotbieUser "force deleted" event.
     *
     * @param  \App\Models\SpotbieUser  $spotbieUser
     * @return void
     */
    public function forceDeleted(SpotbieUser $spotbieUser)
    {
        //
    }
}
