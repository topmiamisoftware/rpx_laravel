<?php

namespace App\Models;

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
        $this->hasMany('App\Models\Users', 'id', 'user_id');
    }

    public function friendList() {
        $this->hasMany('App\Models\Users', 'id', 'friend_id');
    }

    public function business() {
        $this->belongsTo( 'App\Models\Business', 'id', 'business_id');
    }

    public function show(MeetUp $meetUp): Response {
        return response(['meetUp' => $meetUp->with('friendList', function ($query) {
            $query->with('spotbieUser');
        })]);
    }

    public function createMeetUp(Request $request) {
        $validatedData = $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
            'friend_list' => 'required|array',
            'friend_list.*' => 'required|integer|exists:users,id',
            'time' => 'required|date_format:d:H:i',
        ]);

        $user = Auth::user();

        $timeForMeetUp = Carbon::createFromDate($validatedData['time']);

        $conflictingMeetUpList = MeetUp::where('user_id', $user->id)
            ->paginate(20)
            ->whereIn('friend_id', $validatedData['friend_list'])
            ->where('time', '>', $timeForMeetUp->addHours(2))
            ->where('time', '<', $timeForMeetUp->subHours(2))
            ->get();

        if(! is_null($conflictingMeetUpList)) {
            return response([
                'matchUpList' => $conflictingMeetUpList,
                'status' => 'There are conflicting meet ups.'
            ]);
        }

        $friendList = $validatedData['friend_list'];
        $newMeetUpList = [];
        foreach($friendList as $friend) {
            $newMeetUp = new MeetUp();
            $newMeetUp->user_id = $user->id;
            $newMeetUp->friend_id = $friend->id;
            $newMeetUp->business_id = $validatedData['business_id'];
            $newMeetUp->time = $validatedData['time'];
            $newMeetUp->save();
            $newMeetUpList = array_push($newMeetUpList, $newMeetUp);
        }

        return response([
            'matchUpList' => $newMeetUpList
        ]);
    }
}
