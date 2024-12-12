<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class MeetUp extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'friend_id', 'time'];

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
        ]);

        $user = Auth::user();

        $timeForMeetUp = Carbon::createFromDate($validatedData['time']);

        $conflictingMeetUpList = MeetUp::
            where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('friend_id', $user->id);
            })
            ->where('time', '>', $timeForMeetUp->addHours(2))
            ->where('time', '<', $timeForMeetUp->subHours(2))
            ->limit(20)
            ->get();

        if(count($conflictingMeetUpList) > 0) {
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

        $friendList = $validatedData['friend_list'];
        $newMeetUpList = array();
        foreach($friendList as $friend) {
            $newMeetUp = new MeetUp();
            $newMeetUp->user_id = $user->id;
            $newMeetUp->friend_id = $friend;
            $newMeetUp->business_id_sb = $sbId;
            $newMeetUp->business_id = $bId;
            $newMeetUp->time = $timeForMeetUp;
            $newMeetUp->save();
            $newMeetUp->refresh();
            array_push($newMeetUpList, $newMeetUp);
        }

        return response([
            'matchUpList' => $newMeetUpList
        ]);
    }
}
