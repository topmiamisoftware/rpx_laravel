<?php

namespace App\Observers;

use App\Models\SpotbieUser;

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
