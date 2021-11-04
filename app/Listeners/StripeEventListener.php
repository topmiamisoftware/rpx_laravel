<?php

namespace App\Listeners;

use App\Models\Ads;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookReceived;
use Laravel\Cashier\Subscription;
use Stripe\Subscription as StripeSubscription;

class StripeEventListener
{
    /**
     * Handle received Stripe webhooks.
     *
     * @param  \Laravel\Cashier\Events\WebhookReceived  $event
     * @return void
     */
    public function handle(WebhookReceived $event)
    {

        
        /**
         *  
         * We have two types of customers. 
         * 1. First type of stripe customer is the Ads models which each Ad has a different subscription.
         * 2. Second type of stripe customer is the Users models which each Users has a different subscription.
         *
         */

        if ($event->payload['type'] === 'customer.subscription.updated') {
            

            //Try to find the user in the subscription table.
            $subscription = Subscription::select('stripe_id')
            ->where('stripe_id', '=', $event->payload['data']['object']['customer'])
            ->first();
            
            if($subscription !== null){

                $adStripeId = $event->payload['data']['object']['customer'];

                //Let's update the ad according to the subscription update.

                $subscriptionId = $event->payload['data']['object']['id'];                

                $ended_at = $event->payload['data']['object']['ended_at'];

                if($ended_at == null){
                    
                    $isLive = true;

                    $is_subscription = true;
                    $failed_subscription = false;

                } else {

                    $isLive = false;

                    $is_subscription = false;
                    $failed_subscription = true;

                }

                $stripe_product = $event->payload['data']['object']['items']['data'][0]['price']['product'];
                $stripe_price = $event->payload['data']['object']['items']['data'][0]['price']['unit_amount']; 
                $stripe_status = $event->payload['data']['object']['status'];

                $quantity = $event->payload['data']['object']['quantity'];

                $ends_at = $event->payload['data']['object']['current_period_end'];

                $trial_ends_at = $event->payload['data']['object']['trial_end'];

                $adsUpdateArr = [
                    "is_live" => $isLive,
                    "ends_at" => $endsAt,
                    "subscription_id" => $subscriptionId,
                    "stripe_id" => $stripeId,
                    "stripe_product" => $stripe_product,
                    "stripe_price" => $stripe_price,
                    "quantity" => $quantity,
                    "ends_at" => $ends_at
                ];

                $subscriptionUpdateArr = [
                    "stripe_id" => $subscriptionId,
                    "stripe_status" => $stripe_status,
                    "stripe_price" => $stripe_price,
                    "quantity" => $quantity,
                    "trial_ends_at" => $trial_ends_at,
                    "ends_at" => $ends_at
                ];
                
                DB::transaction(function () use ($subscription, $adStripeId, $adsUpdateArr, $subscriptionUpdateArr){
                
                    Ads::where('stripe_id', '=', $adStripeId)
                    ->update($adsUpdateArr);
    
                    Subscription::where('stripe_id', '=', $adStripeId)
                    ->update($subscriptionUpdateArr);    
    
                });

                return;

            }


        } else if($event->payload['type'] === 'customer.created') {

            //The user has created a new subscription.

            //Insert the subscription

            if($event->payload['data']['object']['delinquent'] == true){
                return;
            }

            $customer_id = $event->payload['data']['object']['id'];

            $adSubscription = Ads::select('business_id')
            ->where('stripe_id', '=', $customer_id)
            ->first();           

            $adSubscription->newSubscription()->add();


        } else if($event->payload['type'] === 'customer.subscription.deleted') {
            


        }
    }
}