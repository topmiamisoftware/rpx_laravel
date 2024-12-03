<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class FriendshipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        $user = Auth::user();
        $friendList = Friendship::select('id', 'user_id', 'friend_id')
            ->with('userProfile', function ($query) {
                $query->with('spotbieUser');
            })
            ->where('user_id', $user->id)
            ->orWhere('friend_id', $user->id)
            ->paginate(20)->get();

        return response([
            'friendList' => $friendList,
        ]);
    }

    public function requestFriendship(Friendship $friendship, Request $request): Response {
        return $friendship->requestFriendship($request);
    }

    public function deleteFriendship(Friendship $friendship, Request $request): Response {
        return $friendship->deleteFriendship($request);
    }

    public function acceptFriendship(Friendship $friendship, Request $request): Response {
        return $friendship->acceptFriendship($request);
    }

    public function blockFriendship(Friendship $friendship, Request $request): Response {
        return $friendship->blockFriendship($request);
    }
}
