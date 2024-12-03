<?php

namespace App\Http\Controllers;

use App\Models\MeetUp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

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

        $matchUpList = MeetUp::where('user_id', $user->id)
            ->paginate(20)
            ->get();

        return response([
            'matchUpList' => $matchUpList
        ]);
    }

    public function createMeetUp(Request $request, MeetUp $meetUp): Response {
        return $meetUp->createMeetUp($request);
    }

    public function destroy(Request $request, MeetUp $meetUp): int {
        return $meetUp->destroy($request);
    }

    public function show(MeetUp $meetUp): Response {
        return $meetUp->show($meetUp);
    }
}
