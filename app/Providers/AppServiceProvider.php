<?php

namespace App\Providers;

use App\Models\Ads;
use App\Observers\UserObserver;
use App\Models\User;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;

use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        User::observe(UserObserver::class);
        Cashier::useSubscriptionItemModel(Ads::class);
        Cashier::calculateTaxes();    
    }
}
