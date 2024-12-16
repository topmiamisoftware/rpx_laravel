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

    protected $fillable = ['user_id', 'friend_id', 'time', 'business_id_sb', 'business_id'];

    public function owner() {
        $this->belongsTo('App\Models\User', 'id', 'user_id');
    }

    public function friendList() {
        $this->hasMany('App\Models\User', 'id', 'friend_id');
    }

    public function business() {
        $this->belongsTo( 'App\Models\Business', 'id', 'business_id_sb');
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
            'business_id' => 'required|integer|exists:business,id',
            'sbcm' => 'required|boolean',
            'friend_list' => 'required|array',
            'friend_list.*' => 'required|integer|exists:users,id',
            'time' => 'required|date',
            'contact_list' => 'array',
            'contact_list.*' => 'json',
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
        $newMeetUp->friend_list = json_encode($validatedData['friend_list']);
        $newMeetUp->business_id_sb = $sbId;
        $newMeetUp->business_id = $bId;
        $newMeetUp->time = $timeForMeetUp;
        Log::info('The New Meet Up : '. $newMeetUp);
        $newMeetUp->save();
        $newMeetUp->refresh();

        $meetUpInvitation = $validatedData['friend_list'];
        $newMuiList = array();

        foreach($meetUpInvitation as $mui) {
            $newMui = new MeetUpInvitation();
            $newMui->user_id = $user->id;
            $newMui->friend_id = $mui;
            $newMui->meet_up_id = $newMeetUp->id;
            $newMui->business_id_sb = $sbId;
            $newMui->business_id = $bId;
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
}
