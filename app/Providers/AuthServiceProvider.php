<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [

    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        ResetPassword::createUrlUsing(function ($user, string $token) {
            $frontEnd = config('spotbie.spotbie_front_end_ip');
            return  $frontEnd . 'password/reset/' . $token . '?email=' . urlencode($user->email);
        });

    }
}
