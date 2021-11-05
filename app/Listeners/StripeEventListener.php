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
            


        } else if($event->payload['type'] === 'customer.created') {



        } else if($event->payload['type'] === 'customer.subscription.deleted') {
            


        }
    }
}