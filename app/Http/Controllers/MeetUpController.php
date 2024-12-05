<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\MeetUp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MeetUpController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(): Response
    {
        $user = Auth::user();

        $meetUpListing = MeetUp::join('business', 'business.id', '=', 'meet_ups.business_id')
            ->where('meet_ups.user_id', $user->id)
            ->orWhere('meet_ups.friend_id', $user->id)
            ->paginate(20);

        return response([
            'meetUpListing' => $meetUpListing
        ]);
    }

    public function createMeetUp(Request $request, MeetUp $meetUp): Response {
        return $meetUp->createMeetUp($request);
    }

    public function destroy(Request $request, MeetUp $meetUp): int {
        $validatedDate = $request->validate([
            'meet_up_id' => 'required|integer|exists:meet_ups,id',
        ]);
        return $meetUp->destroy($validatedDate['meet_up_id']);
    }

    public function show(MeetUp $meetUp): Response {
        return $meetUp->show($meetUp);
    }
}
