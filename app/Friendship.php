<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Friendship extends Pivot
{
    
    use HasFactory; 
    
    public function initiatingUser(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'peer_id', 'id');
    }  

    public function spotbieUser(){
        return $this->belongsTo('App\SpotbieUser', 'peer_id', 'id');
    }

    public function notification(){
        return $this->hasOne('App\FriendshipNotification', 'friendship_id');
    }

    public function friendStreamPosts(){
        return $this->hasManyThrough(
            'App\StreamPost',
            'App\User',
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
        ->where('relation', 0)
        ->with('user:id,username')
        ->with('spotbieUser:id,first_name,last_name,default_picture')
        ->paginate(20);

        $response = array(
            'message' => 'success',
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
            'message' => 'success'
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
            ['peer_id' => $validatedData['peer_id']],
            ['relation' => 2]
        );

        $response = array(
            'message' => 'success'
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

        $reportPeer = DB::table('reports')->insert([
            'user_id' =>  $userId,
            'peer_id' => $validatedData['peer_id'],
            'report_reason' => $validatedData['report_reason'],
        ]);
        
        $response = array(
            'message' => 'success',
        );

        return response($response);

    }

}
