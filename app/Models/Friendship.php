<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class Friendship extends Model
{
    use HasFactory;

    protected $fillable = [];

    public function userProfile(){
        return $this->belongsTo( 'user', 'id', 'user_id');
    }

    public function spotbieUserProfile() {
        return $this->belongsTo('spotbie_user','id', 'user_id');
    }

    public function requestFriendship(Request $request): Response {
        $validatedData = $request->validate([
            'friend_id' => 'required|integer',
        ]);

        $user = Auth::user();

        $existing = Friendship::where('user_id', $user->id)
            ->where('friend_id', $validatedData['friend_id'])
            ->where('relationship', config('enums.friendships.PENDING'))
            ->where('relationship', config('enums.friendships.BLOCKED'))
            ->where('relationship', config('enums.friendships.ACCEPTED'))
            ->get();

        if (! is_null($existing)) {
            return response(['status' => 'Friendship already exists.'], 403);
        }


        $friendship = Friendship::where('user_id', $user->id)
            ->where('friend_id', $validatedData['friend_id'])
            ->where('relationship', config('enums.friendships.DECLINED'))
            ->get();

        if (is_null($friendship)) {
            $friendship = new Friendship();
        }

        $friendship->user_id = $validatedData['user_id'];
        $friendship->friend_id = $validatedData['friend_id'];
        $friendship->relationship = config('enums.friendships.PENDING');
        $friendship->save();
        $friendship->refresh();

        return response([
            'friendship' => $friendship,
        ]);
    }

    public function deleteFriendship(Request $request) {
        $validateData = $request->validate([
            'friend_id' => 'required|integer',
        ]);

        $user = Auth::user();

        $friendship = Friendship::
        where('friend_id', $validateData['friend_id'])
            ->where('user_id', $user->id)
            ->where('relationship', config('enums.friendships.ACCEPTED'))
            ->get();

        if (is_null($friendship)) {
            return response(['status' => 'Friendship not found.'], 404);
        }

        $friendship->delete();
        $friendship->refresh();

        return response([
            'friendship' => $friendship
        ]);
    }

    public function acceptFriendship(Request $request) {
        $validateData = $request->validate([
            'friend_id' => 'required|integer',
        ]);

        $user = Auth::user();

        $friendship = Friendship::where('friend_id', $validateData['friend_id'])
            ->where('user_id', $user->id)
            ->where('relationship', config('enums.friendships.PENDING'))
            ->first();

        if (is_null($friendship)) {
            return response([
                'status' => 'This friendship is not pending.',
            ]);
        }

        $friendship->relationship = config('enums.friendships.ACCEPTED');
        $friendship->save();
        $friendship->refresh();

        return response([
            'friendship' => $friendship
        ]);
    }

    public function blockFriendship(Request $request): Response {
        // report and block a person
        $validateData = $request->validate([
            'friend_id' => 'required|integer',
        ]);

        $user = Auth::user();

        $friendship = Friendship::where('friend_id', $validateData['friend_id'])
            ->where('user_id', $user->id)
            ->where('relationship', config('enums.friendships.BLOCKED'))
            ->first();

        if (! is_null($friendship)) {
            return response([
                'status' => 'You have already blocked this user.'
            ], 403);
        }

        $friendship = Friendship::where('friend_id', $validateData['friend_id'])
            ->where('user_id', $user->id)
            ->first();

        if (is_null($friendship)) {
            $friendship = new Friendship();
        }

        $friendship->relationship = config('enums.friendships.BLOCKED');
        $friendship->save();
        $friendship->refresh();

        return response([
            'friendship' => $friendship
        ]);
    }
}
