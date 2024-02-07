<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use Auth;
use App\Jobs\SendMassSms;
use App\Models\Business;
use App\Models\SpotbieUser;
use App\Models\LoyaltyPointBalance;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomerManager extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $business = $user->business;
        $customerList = LoyaltyPointBalance::where('from_business', $business->id)->with(['user' => function (MorphTo $morphTo) {
            $morphTo->morphWith([
                SpotbieUser::class => ['spotbieUser'],
            ]);
        }])->get();

        return response([
            'customerList' => $customerList
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Send recent customers of the business an SMS message.
     */
    public function sms(Sms $sms, Request $request)
    {
        $validated = $this->validate($request, [
            "sms" => 'required|string|max:320|min:20',
        ]);

        $smsText = $validated['sms'];

        $user = Auth::user();

        try {
            $user->business()->with('recentGuests')->each(function (Business $business) use ($smsText, $sms, $request) {
                $business->recentGuests->each(function (LoyaltyPointBalance $lpBalance) use ($smsText, $sms, $request, $business) {
                    $user = $lpBalance->user()->first();
                    $spotbieUser = $user->spotbieUser()->first();
                    $phoneNumber = $spotbieUser->phone_number;
                    if (! is_null($phoneNumber)) {
                        $sms = $sms->createNewSms($request, $user, $business);
                        SendMassSms::dispatch($user, $business->name, $sms)
                            ->onQueue('sms.miami.fl.1');
                    } else {
                        Log::info(
                            '[CustomerManager]-[SendMassSms]: User ID: ' .
                            $user->id .
                            ", Business: " . $business->name .
                            ", Error: No User Phone Number"
                        );
                    }
                });
            });

            return response([
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            Log::info(
                '[CustomerManager]-[sendSms]: User ID: '. $user->id .
                ', Error: ' . $e->getCode() .
                ', Message: ' . $e->getMessage() .
                ', Could not send message.'
            );

            return response([
                'success' => false,
                'error' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }
}
