<?php

namespace App\Http\Controllers;

use App\Friendship;
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


    public function report(Friendship $friendships, Request $request){
        return $friendships->report($request);
    }

    

}
