<?php

namespace App\Models;

use Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;

use App\Models\Reward;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Cashier\Cashier;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'business';

    protected $fillable = ['photo'];

    public function rewards(){
        return $this->hasMany('App\Models\Reward', 'business_id', 'id');
    }

    public function ads(){
        return $this->hasMany('App\Models\Ads', 'business_id');
    }

    public function user(){
        return $this->belongsTo('App\Models\User', 'id');
    }

    public function spotbieUser(){
        return $this->belongsTo('App\Models\SpotbieUser', 'id');
    }

    public function verify(Request $request){

        /*

            1. Create the new model
            2. Update the existing model
            3. Verifying the business.

        */

        $validatedData = $request->validate([
            'name' => 'required|string|max:75|min:1',
            'description' => 'required|string|max:350|min:1',
            'address' => 'required|string|max:350|min:1',
            'city' => 'required|string',
            'country' => 'required|string',
            'line1' => 'nullable|string',
            'line2' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'state' => 'nullable|string',
            'photo' => 'required|string|max:650|min:1',
            'loc_x' => 'required|max:90|min:-90|numeric',
            'loc_y' => 'required|max:180|min:-180|numeric',
            'categories' => 'required|string',
            'passkey' => 'required|string|max:20|min:4',
            'accountType' => 'required|numeric'
        ]);

        $user = Auth::user();

        $confirmKeyLifeTime = 'ITS-ON-US-BRO';

        $confirmKey = 'K23' . $user->id;

        $spotbieBusinessPassKey = $confirmKey;

        if(
            $spotbieBusinessPassKey !== $validatedData['passkey'] &&
            $confirmKeyLifeTime !== $validatedData['passkey']
        ){
            $response = array(
                'message' => 'passkey_mismatch'
            );
            return response($response);
        }

        $isLifeTimeMembership = false;

        if( $confirmKeyLifeTime === $validatedData['passkey'] ){
            $isLifeTimeMembership = true;
        } else if( $spotbieBusinessPassKey === $validatedData['passkey'] ) {
            $isLifeTimeMembership = false;
        }

        $user->spotbieUser->user_type =  $validatedData['accountType'];

        //check if the place to eat already exists.
        $existingBusiness = $user->business;

        if( !is_null($existingBusiness) )
            $business = $user->business;
        else
            $business = new Business();

        $business->id = $user->id;
        $business->name = $validatedData['name'];
        $business->description = $validatedData['description'];
        $business->slug = Str::slug($business->name);

        $business->address = $validatedData['address'];

        $business->city = $validatedData['city'];
        $business->country = $validatedData['country'];
        $business->line1 = $validatedData['line1'];
        $business->line2 = $validatedData['line2'];
        $business->postal_code = $validatedData['postal_code'];
        $business->state = $validatedData['state'];

        $business->photo = $validatedData['photo'];
        $business->loc_x = $validatedData['loc_x'];
        $business->loc_y = $validatedData['loc_y'];
        $business->categories = $validatedData['categories'];
        $business->is_verified = 1;

        $business->qr_code_link = Str::uuid();

        $giveTrial = false;

        if($existingBusiness){

            DB::transaction(function () use ($business, $user){
                $user->business->save();
                $user->spotbieUser->save();
            }, 3);

        } else {

            $user->trial_ends_at = Carbon::now()->addDays(90);

            DB::transaction(function () use ($business, $user){
                $business->save();
                $user->spotbieUser->save();
                $user->save();
            }, 3);

            $giveTrial = true;
        }

        $userBillable = Cashier::findBillable($user->stripe_id);

        $existingSubscription = $userBillable->subscriptions()->where('name', '=', $user->id)->first();

        //Check if the user entered a lifetime membership passkey
        if( $isLifeTimeMembership && !is_null($existingSubscription) ){

            //Extend the user's trial for a lifetime
            $user = User::find($user->id);
            $user->trial_ends_at = Carbon::now()->addYears(90);

            DB::transaction(function () use ($user){
                $user->save();
            }, 3);

        } else if($isLifeTimeMembership) {

            //Extend the user's trial for a lifetime
            $user = User::find($user->id);
            $user->trial_ends_at = Carbon::now()->addYears(90);

            DB::transaction(function () use ($user){
                $user->save();
            }, 3);

        }

        $response = array(
            'message' => 'success',
            'business' => $business,
            'giveTrial' => $giveTrial,
        );

        return response($response);

    }

    public function saveBusiness(Request $request){

        /*
            1. Create the new model
            2. Update the existing model
        */

        $validatedData = $request->validate([
            'name' => 'required|string|max:75|min:1',
            'description' => 'required|string|max:350|min:1',
            'address' => 'required|string|max:350|min:1',
            'city' => 'required|string',
            'country' => 'required|string',
            'line1' => 'nullable|string',
            'line2' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'state' => 'nullable|string',
            'photo' => 'required|string|max:650|min:1',
            'loc_x' => 'required|max:90|min:-90|numeric',
            'loc_y' => 'required|max:180|min:-180|numeric',
            'categories' => 'required|string',
            'accountType' => 'required|numeric'
        ]);

        $user = Auth::user();

        $user->spotbieUser->user_type = $validatedData['accountType'];

        //check if the place to eat already exists.
        $existingBusiness = $user->business;

        if( !is_null($existingBusiness) )
            $business = $user->business;
        else
            $business = new Business();

        $business->id = $user->id;
        $business->name = $validatedData['name'];
        $business->description = $validatedData['description'];
        $business->slug = Str::slug($business->name);

        $business->address = $validatedData['address'];

        $business->city = $validatedData['city'];
        $business->country = $validatedData['country'];
        $business->line1 = $validatedData['line1'];
        $business->line2 = $validatedData['line2'];
        $business->postal_code = $validatedData['postal_code'];
        $business->state = $validatedData['state'];

        $business->photo = $validatedData['photo'];
        $business->loc_x = $validatedData['loc_x'];
        $business->loc_y = $validatedData['loc_y'];
        $business->categories = $validatedData['categories'];

        $business->is_verified = 0;

        $business->qr_code_link = Str::uuid();

        DB::transaction(function () use ($business, $user){
            $business->save();
            $user->spotbieUser->save();
        }, 3);

        $response = array(
            'message' => 'success',
            'business' => $business
        );

        return response($response);

    }


    public function getGooglePlacesToEat(Request $request){

        $request->validate([
            'url'    => 'required|string|max:250|min:1',
            'bearer' => 'required|string|max:250|min:1'
        ]);

        $url = $request->url;
        $gToken = $request->bearer;

        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, $url);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Access-Control-Allow-Credentials: true',
            'Content-Type: application/json',
            "Authorization: Bearer $gToken"
        ));

        // $output contains the output string
        $output = curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);

        $response = array(
            'message' => 'success',
            'g_response' => $output
        );

        return response($response);

    }

    public function show(Request $request){

        $validatedData = $request->validate([
            'qrCodeLink' => ['required', 'string']
        ]);

        $business = Business::select(
            'business.qr_code_link', 'business.name', 'business.categories', 'business.description',
            'business.photo', 'business.qr_code_link', 'business.loc_x', 'business.loc_y',
            'spotbie_users.user_type',
            'loyalty_point_balances.balance', 'loyalty_point_balances.loyalty_point_dollar_percent_value',
        )
        ->join('spotbie_users', 'business.id', '=', 'spotbie_users.id')
        ->join('loyalty_point_balances', 'business.id', '=', 'loyalty_point_balances.id')
        ->where('qr_code_link', $validatedData['qrCodeLink'])
        ->get()[0];

        if($business){
            $success = true;
        } else {
            $success = false;
        }

        $response = array(
            'success' => $success,
            'business' => $business
        );

        return response($response);
    }
}
