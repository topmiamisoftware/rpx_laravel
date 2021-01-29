<?php

namespace App\Http\Controllers\Friendship;

use App\Http\Controllers\Controller;

use App\Models\Friendship;
use Illuminate\Http\Request;

class FriendshipsController extends Controller
{

    public function showFriends(Friendship $friendships){
        return $friendships->showFriends();
    }

    public function showPending(Friendship $friendships){
        return $friendships->showPending();
    }

    public function showBlocked(Friendship $friendships){
        return $friendships->showBlocked();
    }

    public function showNearby(Friendship $friendships, Request $request){
        return $friendships->showNearby($request);
    }

    public function unfriend(Friendship $friendships, Request $request){
        return $friendships->unfriend($request);
    }

    public function block(Friendship $friendships, Request $request){
        return $friendships->block($request);
    }

    public function unblock(Friendship $friendships, Request $request){
        return $friendships->unblock($request);
    }

    public function report(Friendship $friendships, Request $request){
        return $friendships->report($request);
    }

    public function cancelRequest(Friendship $friendships, Request $request){
        return $friendships->cancelRequest($request);
    }

    public function acceptRequest(Friendship $friendships, Request $request){
        return $friendships->acceptRequest($request);
    }

    public function declineRequest(Friendship $friendships, Request $request){
        return $friendships->declineRequest($request);
    }

    public function checkRelationship(Friendship $friendships, Request $request){
        return $friendships->checkRelationship($request);
    }
    
    public function addFriend(Friendship $friendships, Request $request){
        return $friendships->addFriend($request);
    }


}
