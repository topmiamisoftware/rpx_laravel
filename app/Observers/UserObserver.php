<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserLocation;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function created(User $user)
    {
        //Create User Location
        $userLocation = new UserLocation();
        $userLocation->createUserLocation($user);
    }

    /**
     * Handle the user "updated" event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function updated(User $user)
    {
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function deleted(User $user)
    {
    }

    /**
     * Handle the user "restored" event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function restored(User $user)
    {
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param \App\Models\User $user
     *
     * @return void
     */
    public function forceDeleted(User $user)
    {
    }
}
