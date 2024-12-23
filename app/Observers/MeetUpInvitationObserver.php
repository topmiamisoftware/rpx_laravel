<?php

namespace App\Observers;

use App\Jobs\SendMeetUpInvitation;
use App\Models\Business;
use App\Models\MeetUp;
use App\Models\MeetUpInvitation;
use App\Models\User;
use App\Services\SurroundingsApi;
use Illuminate\Support\Facades\Log;

class MeetUpInvitationObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param \App\Models\MeetUpInvitation $meetUpInvitation
     *
     * @return void
     */
    public function created(MeetUpInvitation $meetUpInvitation)
    {
        $meetUpOwner = $meetUpInvitation->ownerAccount;
        $meetUp = MeetUp::find($meetUpInvitation->meet_up_id);

        $guestName = null;
        $friendIdForInv = User::find($meetUpInvitation->friend_id);

        if(! is_null($friendIdForInv)) {
            if (! is_null($friendIdForInv->spotbieUser->first_name )) {
                $guestName = $friendIdForInv->spotbieUser->first_name . " " . $friendIdForInv->spotbieUser->last_name;
            } else {
                $guestName = User::find($meetUpInvitation->friend_id);
            }
        } else {
            // Get the guest name from the contact list
            $guestName = 'there';
        }

        $invitationList = json_decode($meetUp->friend_list);
        $invitationListNameList = array();

        foreach ($invitationList as $invitation) {
            $invitedUser = User::find($invitation);

            $invitedUserProfile = $invitedUser->spotbieUser;
            $invitedName = null;

            // First Name and Last Name are not required, but usernames are.
            if (! is_null($invitedUserProfile->first_name)) {
                $invitedName = $invitedUserProfile->first_name . " " . $invitedUserProfile->last_name;
            } else {
                $invitedName = $meetUpOwner->username;
            }

            array_push($invitationListNameList, $invitedName);
        }

        $invitationContactList = json_decode($meetUp->contact_list);
        if (! is_null($invitationContactList)) {
            foreach ($invitationContactList as $invitation) {
                array_push($invitationListNameList, $invitation->name);
            }
        }

        $business = null;
        if (! is_null($meetUp->business_id_sb) ) {
            $business = Business::find($meetUp->business_id_sb);
            $businessName = $business->name;
        } else {
            $businessId = $meetUp->business_id;
            $yelpConfigUrl = "https://api.yelp.com/v3/businesses/$businessId";
            $business = app(SurroundingsApi::class)->pullInfoObject($yelpConfigUrl);
            Log::info("Yelp Business URL: " . $yelpConfigUrl);
            Log::info("Yelp Business: " . json_encode($business));
            $businessName = $business->name;
        }

        $invitationListNameList = implode(', ', $invitationListNameList);

        Log::info('Meet Up Owner: ' . $meetUpOwner);
        Log::info('Meet Up: ' . $meetUp);
        Log::info('Meet Up Invitation: ' . $meetUpInvitation);
        Log::info('Invitation List: ' . $invitationListNameList);
        Log::info('Business Name: ' . $businessName);

        // Create MeetUp Invitation SMS Job
        SendMeetUpInvitation::dispatch($meetUpOwner, $meetUp, $meetUpInvitation, $invitationListNameList, $businessName, $guestName)
            ->onQueue(config('spotbie.sms.queue'));
    }

    /**
     * Handle the user "updated" event.
     *
     * @param \App\Models\MeetUpInvitation $meetUpInvitation
     *
     * @return void
     */
    public function updated(MeetUpInvitation $meetUpInvitation)
    {
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param \App\Models\MeetUpInvitation $meetUpInvitation
     *
     * @return void
     */
    public function deleted(MeetUpInvitation $meetUpInvitation)
    {
    }

    /**
     * Handle the user "restored" event.
     *
     * @param \App\Models\MeetUpInvitation $meetUpInvitation
     *
     * @return void
     */
    public function restored(MeetUpInvitation $meetUpInvitation)
    {
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param \App\Models\MeetUpInvitation $meetUpInvitation
     *
     * @return void
     */
    public function forceDeleted(MeetUpInvitation $meetUpInvitation)
    {
    }
}
