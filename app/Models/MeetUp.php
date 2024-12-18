<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MeetUp extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'friend_list', 'contact_list', 'time', 'business_id_sb', 'business_id'];

    public function owner() {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function invitationList() {
        return $this->hasMany('App\Models\MeetUpInvitation', 'meet_up_id', 'id');
    }

    public function business() {
        return $this->belongsTo( 'App\Models\Business', 'business_id_sb', 'id');
    }

    public function show(MeetUp $meetUp): Response {
        return response(['meetUp' => $meetUp->with('friendList', function ($query) {
            $query->with('spotbieUser');
        })]);
    }

    public function createMeetUp(Request $request) {
        $validatedData = $request->validate([
            'meet_up_name' => 'required|string|max:35',
            'meet_up_description' => 'required|string|max:350',
            'business_id' => 'required|string',
            'sbcm' => 'required|boolean',
            'friend_list' => 'nullable|array',
            'friend_list.*' => 'nullable|integer',
            'time' => 'required|date',
            'contact_list' => 'array|nullable',
            'contact_list.*' => 'json|nullable',
        ]);

        $user = Auth::user();

        $timeForMeetUp = Carbon::createFromDate($validatedData['time']);

        $conflictingMeetUpList = MeetUp::select([
            'meet_ups.*',
            'mui.*',
        ])
            ->join('meet_up_invitations as mui', 'mui.meet_up_id', '=', 'meet_ups.id')
            ->where(function ($query) use ($user) {
                $query->where('mui.user_id', $user->id)
                    ->orWhere('mui.friend_id', $user->id);
            })
            ->where('meet_ups.time', '>', $timeForMeetUp->addHours(2))
            ->where('time', '<', $timeForMeetUp->subHours(2))
            ->limit(20)
            ->get();

        if( count($conflictingMeetUpList) > 0) {
            return response([
                'conflictingMatchUpList' => $conflictingMeetUpList,
                'status' => 'There are conflicting meet ups.'
            ]);
        }

        if ($validatedData['sbcm'] === true) {
            $sbId = $validatedData['business_id'];
            $bId = null;
        } else {
            $sbId = null;
            $bId = $validatedData['business_id'];;
        }

        $newMeetUp = new MeetUp();
        $newMeetUp->user_id = $user->id;
        $newMeetUp->friend_list = (!is_null($validatedData['friend_list'])) ? json_encode($validatedData['friend_list']) : null;
        $newMeetUp->contact_list = (!is_null($validatedData['contact_list'])) ? json_encode($validatedData['contact_list']) : null;
        $newMeetUp->business_id_sb = $sbId;
        $newMeetUp->business_id = $bId;
        $newMeetUp->time = $timeForMeetUp;
        $newMeetUp->description = $validatedData['meet_up_description'];
        $newMeetUp->name = $validatedData['meet_up_name'];
        $newMeetUp->save();
        $newMeetUp->refresh();

        if (!is_null($validatedData['contact_list']) && count($validatedData['contact_list']) > 0) {
            $phoneNumbersOnly = $this->mapToPhoneOnly($validatedData['contact_list']);
            $meetUpInvitation = array_merge($validatedData['friend_list'], $phoneNumbersOnly);
        } else {
            $meetUpInvitation = $validatedData['friend_list'];
        }

        $newMuiList = array();

        foreach($meetUpInvitation as $mui) {
            $newMui = new MeetUpInvitation();
            $newMui->user_id = $user->id;
            $newMui->friend_id = $mui;
            $newMui->meet_up_id = $newMeetUp->id;
            $newMui->going = false;
            $newMui->save();
            $newMui->refresh();
            array_push($newMuiList, $newMui);
        }

        return response([
            'meetUp' => $newMeetUp,
            'meetUpInvitationList' => $newMuiList,
        ]);
    }

    public function editMeetUp(Request $request) {
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:meet_ups,id',
            'meet_up_name' => 'required|string|max:35',
            'meet_up_description' => 'required|string|max:350',
            'friend_list' => 'nullable|array',
            'friend_list.*' => 'nullable|integer',
            'time' => 'required|date',
            'contact_list' => 'array|nullable',
            'contact_list.*' => 'json|nullable',
        ]);

        $user = Auth::user();

        $meetUp = MeetUp::find($validatedData['id']);

        if ($meetUp->user_id !== $user->id) {
            return response([
                'message' => 'You cannot edit this meet up.',
            ], 403);
        }

        $timeForMeetUp = Carbon::createFromDate($validatedData['time']);

        $conflictingMeetUpList = MeetUp::select([
            'meet_ups.*',
            'mui.*',
        ])
            ->join('meet_up_invitations as mui', 'mui.meet_up_id', '=', 'meet_ups.id')
            ->where(function ($query) use ($user) {
                $query->where('mui.user_id', $user->id)
                    ->orWhere('mui.friend_id', $user->id);
            })
            ->where('meet_ups.time', '>', $timeForMeetUp->addHours(2))
            ->where('time', '<', $timeForMeetUp->subHours(2))
            ->limit(20)
            ->get();

        if (count($conflictingMeetUpList) > 0) {
            return response([
                'conflictingMatchUpList' => $conflictingMeetUpList,
                'status' => 'There are conflicting meet ups.'
            ]);
        }

        $meetUp->user_id = $user->id;
        $meetUp->friend_list = (!is_null($validatedData['friend_list'])) ? json_encode($validatedData['friend_list']) : null;
        $meetUp->contact_list = (!is_null($validatedData['contact_list'])) ? json_encode($validatedData['contact_list']) : null;
        $meetUp->time = $timeForMeetUp;
        $meetUp->description = $validatedData['meet_up_description'];
        $meetUp->name = $validatedData['meet_up_name'];
        $meetUp->save();
        $meetUp->refresh();

        // Let's delete the ones that were removed
        $alreadyInvited = MeetUpInvitation::where(function ($qry) use ($user, $meetUp){
            $qry->where('user_id', $user->id)
                ->orWhere('friend_id', $user->id);
        })->where('meet_up_id', $meetUp->id)->get()->pluck('friend_id')->toArray();

        $notInNewList = array_diff($alreadyInvited, $validatedData['friend_list']);

        foreach ($notInNewList as $key => $invId) {
            MeetUpInvitation::where('friend_id', $invId)->where('meet_up_id', $meetUp->id)->delete();
        }

        if (!is_null($validatedData['contact_list']) && count($validatedData['contact_list']) > 0) {
            $phoneNumbersOnly = $this->mapToPhoneOnly($validatedData['contact_list']);
            $meetUpInvitation = array_merge($validatedData['friend_list'], $phoneNumbersOnly);
        } else {
            $meetUpInvitation = $validatedData['friend_list'];
        }

        $newMuiList = array();

        foreach($meetUpInvitation as $mui) {
            $e = MeetUpInvitation::where(function ($qry) use ($mui, $meetUp, $user){
                $qry->where('user_id', $user->id)
                    ->where('friend_id', $mui);
            })->where('meet_up_id', $meetUp->id)->get();

            if (count($e) > 0) {
                continue;
            }

            $e = new MeetUpInvitation();
            $e->user_id = $user->id;
            $e->friend_id = $mui;
            $e->meet_up_id = $meetUp->id;
            $e->going = false;
            $e->save();
            $e->refresh();

            array_push($newMuiList, $e);
        }

        return response([
            'meetUp' => $meetUp,
            'meetUpInvitationList' => $newMuiList,
        ]);
    }

    function mapToPhoneOnly($contactList) {
        $a = array();

        foreach ($contactList as $contact) {
            $c = json_decode($contact);
            array_push( $a, $c->number);
        }

        return $a;
    }
}
