<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

use App\Models\Report;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\SoftDeletes;

class Friendship extends Pivot
{
    
    use HasFactory, SoftDeletes; 
    
    public function initiatingUser(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function user(){
        return $this->belongsTo('App\Models\User', 'peer_id', 'id');
    }  

    public function spotbieUser(){
        return $this->belongsTo('App\Models\SpotbieUser', 'peer_id', 'id');
    }

    public function notification(){
        return $this->hasOne('App\Models\FriendshipNotification', 'friendship_id');
    }

    public function friendStreamPosts(){
        return $this->hasManyThrough(
            'App\Models\StreamPost',
            'App\Models\User',
            'id',
            'user_id',
            'peer_id',
            'id'
        );
    }

    public function showFriends(){

        $user = Auth::user();

        $friendshipList = $user->relationships()
        ->select('user_id', 'peer_id', 'updated_at')
        ->where('relation', 1)
        ->with('user:id,username')
        ->with('spotbieUser:id,first_name,last_name,default_picture')
        ->paginate(20);

        $response = array(
            'message' => 'success',
            'friend_list' => $friendshipList
        );

        return response($response);

    }

    public function showPending(){

        $user = Auth::user();
        
        $pendingFriendshipList = $user
        ->relationships()
        ->select('user_id', 'peer_id', 'updated_at')
        ->where('relation', 0)
        ->with('user:id,username')
        ->with('spotbieUser:id,first_name,last_name,default_picture')
        ->paginate(20);

        $response = array(
            'success' => true,
            'pending_friends_list' => $pendingFriendshipList
        );

        return response($response);

    }
    
    public function showBlocked(){

        $user = Auth::user();

        $blockedFriendshipList = $user
        ->relationships()
        ->where('relation', 2)
        ->with('user:id,username')
        ->with('spotbieUser:id,first_name,last_name,default_picture')
        ->paginate(20);

        $response = array(
            'message' => 'success',
            'blocked_friendships_list' => $blockedFriendshipList
        );

        return response($response);

    }

    public function showNearby(Request $request){
       
        $user = Auth::user();

        $validatedData = $request->validate([
            'loc_x' => 'required|max:90|min:-90|numeric',
            'loc_y' => 'required|max:180|min:-180|numeric'
        ]);

        $xr = '';
        
        $loc_x = $validatedData['loc_x'];
        $loc_y = $validatedData['loc_y'];

        $aroundMeFriendshipList = $user
        ->relationships()
        ->where('relation', 1)
        ->join('user_locations', 'peer_id', 'user_locations.user_id')
        ->join('spotbie_users', 'user_locations.user_id', 'spotbie_users.id')
        ->join('users', 'spotbie_users.id', 'users.id')
        ->whereRaw("((user_locations.user_id = 2 OR user_locations.user_id = 1
        OR ($xr user_locations.user_id != 0 AND user_locations.loc_x = $loc_x AND user_locations.loc_y = $loc_y)
        OR ($xr user_locations.user_id != 0 
        AND (ABS(SQRT(((POWER((user_locations.loc_x - $loc_x), 2)) + (POWER ((user_locations.loc_y - $loc_y), 2))))) <= 0.01))))")    
        ->select('users.id', 'users.username', 'spotbie_users.first_name', 'spotbie_users.last_name', 'spotbie_users.default_picture')
        ->paginate(20);

        $response = array(
            'message' => 'success',
            'around_me_friend_list' => $aroundMeFriendshipList
        );

        return response($response);

    }

    public function unfriend(Request $request){

        $user = Auth::user();

        $validatedData = $request->validate([
            'peer_id' => 'required|numeric'
        ]);

        $removeFriend = $user
        ->relationships()
        ->where('relation', 1) 
        ->where('peer_id', $validatedData['peer_id'])
        ->delete();

        $response = array(
            'success' =>  true
        );

        return response($response);

    }

    public function block(Request $request){

        $user = Auth::user();

        $userId = $user->id;

        $validatedData = $request->validate([
            'peer_id' => 'required|numeric'
        ]);

        $blockPeer = $user
        ->relationships()
        ->updateOrInsert(
            ['user_id' => $userId, 'peer_id' => $validatedData['peer_id']],
            ['relation' => 2]
        );

        $response = array(
            'message' => 'success'
        );

        return response($response);

    }

    public function unblock(Request $request){

        $user = Auth::user();

        $userId = $user->id;

        $validatedData = $request->validate([
            'peer_id' => 'required|numeric'
        ]);

        $blockPeer = $user
        ->relationships()
        ->where('peer_id', $validatedData['peer_id'])
        ->where('relation', 2)
        ->delete();

        $response = array(
            'success' => true
        );

        return response($response);

    }

    public function report(Request $request){

        $user = Auth::user();

        $userId = $user->id;

        $validatedData = $request->validate([
            'peer_id' => 'required|numeric',
            'report_reason' => 'required|numeric'
        ]);

        $reportPeer = Report::updateOrCreate(
            [
                'user_id' =>  $userId,
                'peer_id' => $validatedData['peer_id']
            ],
            [
                'report_reason' => $validatedData['report_reason'],
                'created_at' =>  Carbon::now(),
                'updated_at' => Carbon::now()           
            ]
        );
        
        $response = array(
            'message' => 'success',
        );

        return response($response);

    }

    public function checkRelationship(Request $request){

        $user = Auth::user();

        $userId = $user->id;

        $validatedData = $request->validate([
            'peer_id' => 'required|numeric'
        ]);
        
        $relationship = $user->relationships()
        ->select('user_id', 'peer_id', 'relation')
        ->where('user_id', $userId)
        ->where('peer_id', $validatedData['peer_id'])
        ->first();
        
        if($relationship === null)
            $relation = null;
        else
            $relation = $relationship->relation;

        $response = array(
            'success' => true,
            'relationship' => $relation
        );

        return response($response);

    }

    public function acceptRequest(Request $request){

        $user = Auth::user();

        $userId = $user->id;

        $validatedData = $request->validate([
            'user_id' => 'required|numeric'
        ]);

        $user->relationships()
        ->where('user_id', $validatedData['user_id']) 
        ->where('peer_id', $userId)
        ->where('relation', 0)
        ->update(['relation' => 1]);

        $response = array(
            'success' => true
        );

        return response($response);

    }

    public function declineRequest(Request $request){

        $user = Auth::user();

        $userId = $user->id;

        $validatedData = $request->validate([
            'peer_id' => 'required|numeric'
        ]);

        $user->relationships()
        ->where('peer_id', $validatedData['peer_id'])
        ->where('relation', 0)
        ->delete();

        $response = array(
            'success' => true
        );

        return response($response);

    }

    public function cancelRequest(Request $request){

        $user = Auth::user();

        $userId = $user->id;

        $validatedData = $request->validate([
            'peer_id' => 'required|numeric'
        ]);

        $user->relationships()
        ->where('peer_id', $validatedData['peer_id'])
        ->where('relation', 0)
        ->delete();

        $response = array(
            'success' => true
        );

        return response($response);

    }

    public function addFriend(Request $request){

        $user = Auth::user();

        $userId = $user->id;

        $validatedData = $request->validate([
            'peer_id' => 'required|numeric'
        ]);
        
        $user->relationships()
        ->updateOrInsert(
            ['user_id' => $userId, 'peer_id' => $validatedData['peer_id']],
            ['relation' => 0]
        );

        $response = array(
            'success' => true
        );

        return response($response);

    }

}
