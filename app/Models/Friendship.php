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
        return $this->belongsTo( '\App\Models\User', 'user_id', 'id');
    }

    public function friendProfile(){
        return $this->belongsTo( '\App\Models\User', 'friend_id', 'id');
    }

    public function requestFriendship(Request $request): Response {
        $validatedData = $request->validate([
            'friend_id' => 'required|integer',
        ]);

        $user = Auth::user();

        $existing = Friendship::where('user_id', $user->id)
            ->where('friend_id', $validatedData['friend_id'])
            ->get();

        if (count($existing) > 0) {
            return response(['status' => 'Friendship already exists.'], 403);
        }

        $friendship = new Friendship();
        $friendship->user_id = $user->id;
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

        $friendship = Friendship::where('friend_id', $validateData['friend_id'])
            ->where('user_id', $user->id)
            ->first();

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

        $friendship = Friendship::where(function ($query) use ($validateData, $user) {
                $query->where('friend_id', $validateData['friend_id'])
                    ->orWhere('friend_id', $user->id);
            })
            ->where(function ($query) use ($validateData, $user) {
                $query->where('user_id', $validateData['friend_id'])
                    ->orWhere('user_id', $user->id);
            })
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

        $friendship = Friendship::where(function ($query) use ($validateData, $user) {
                $query->where('user_id', $validateData['friend_id'])
                    ->orWhere('user_id', $user->id);
            })
            ->where(function ($query) use ($validateData, $user) {
                $query->where('friend_id', $validateData['friend_id'])
                    ->orWhere('friend_id', $user->id);
            })
            ->where('relationship', config('enums.friendships.BLOCKED'))
            ->first();

        if (! is_null($friendship)) {
            return response([
                'status' => 'You have already blocked this user.'
            ], 403);
        }

        $friendship = Friendship::where(function ($query) use ($validateData, $user) {
            $query->where('user_id', $validateData['friend_id'])
                ->orWhere('user_id', $user->id);
            })
            ->where(function ($query) use ($validateData, $user) {
                $query->where('friend_id', $validateData['friend_id'])
                    ->orWhere('friend_id', $user->id);
            })
            ->first();

        if (is_null($friendship)) {
            $friendship = new Friendship();
        }

        $friendship->user_id = $user->id;
        $friendship->friend_id = $validateData['friend_id'];
        $friendship->relationship = config('enums.friendships.BLOCKED');
        $friendship->save();
        $friendship->refresh();

        return response([
            'friendship' => $friendship
        ]);
    }

    public function searchforUser(Request $request) {
        $validatedData = $request->validate([
            'search_string' => 'required|string',
        ]);

        $user = Auth::user();

        $matchingUserList = User::join('spotbie_users', 'spotbie_users.id', '=', 'users.id')
            ->where('users.username', 'like', '%' . $validatedData['search_string'] . '%')
            ->orWhere('spotbie_users.first_name', 'like', '%' . $validatedData['search_string'] . '%')
            ->orWhere('spotbie_users.last_name', 'like', '%' . $validatedData['search_string'] . '%')
            ->with('spotbieUser')
            ->get();

        return response([
            'matchingUserList' => $matchingUserList,
        ]);
    }

    public function randomNearby(Request $request) {
        $validatedData = $request->validate([
            'loc_x' => 'required|max:90|min:-90|numeric',
            'loc_y' => 'required|max:180|min:-180|numeric',
        ]);

        $user = Auth::user();
        $loc_x = $validatedData['loc_x'];
        $loc_y = $validatedData['loc_y'];

        $randomNearby = UserLocation::join('spotbie_users', 'spotbie_users.id', '=', 'user_locations.id')
            ->where('spotbie_users.user_type', 4)
            ->whereRaw("(
                (loc_x = $loc_x AND loc_y = $loc_y)
                OR (
                        ABS (
                                SQRT    (
                                            (POWER ( (loc_x - $loc_x), 2) ) +
                                            (POWER ( (loc_y - $loc_y), 2) )
                                        )
                            )
                        <= 0.5
                    )

                )")
            ->inRandomOrder()
            ->paginate(20);

        $matchingUserList = User::whereIn('id', $randomNearby->pluck('id'))
            ->where('id', '!=', $user->id)
            ->with('spotbieUser')
            ->get();

        return response([
            'matchingUserList' => $matchingUserList,
        ]);
    }
}
