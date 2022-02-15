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
            
            IMPORTANT: 
        
            We are using this method for 3 different pourposes. 
            1. Create the new model
            2. Update the existing model
            3. Verifying the business.

            Please Let's split this method into three different ones so that we adhere to SOLID programming principles.

            Thank you.

        */

        $validatedData = $request->validate([
            'name' => 'required|string|max:75|min:1',
            'description' => 'required|string|max:350|min:1',
            'address' => 'required|string|max:350|min:1',
            'city' => 'required|string',
            'country' => 'required|string',
            'line1' => 'nullable|string',
            'line2' => 'nullable|string',
            'postal_code' => 'required|string',
            'state' => 'required|string',            
            'photo' => 'required|string|max:650|min:1',
            'loc_x' => 'required|max:90|min:-90|numeric',
            'loc_y' => 'required|max:180|min:-180|numeric',
            'categories' => 'required|string',
            'passkey' => 'required|string|max:20|min:4'
        ]);

        $user = Auth::user();

        $confirmKeyLifeTime = 'SPB1DS88' . $user->id;

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

        $user->spotbieUser->user_type = 1;

        //check if the place to eat already exists.
        $existingBusiness = $user->business;        

        if(!is_null($existingBusiness))
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
            
            if($isLifeTimeMembership){
                $user->trial_ends_at = Carbon::now()->addYears(90); 
            }

            DB::transaction(function () use ($business, $user){
                $user->business->save();
                $user->spotbieUser->save();  
                $user->save();              
            }, 3);

        } else {

            //It's a new business we are creating.
            if($isLifeTimeMembership){
                $user->trial_ends_at = Carbon::now()->addYears(90)->endOfDay();
            } else {
                $user->trial_ends_at = Carbon::now()->addDays(90);
            }
            
            DB::transaction(function () use ($business, $user){
                $business->save();
                $user->spotbieUser->save();
                $user->save();
            }, 3);  

            $giveTrial = true;

        }
        
        $response = array(
            'message' => 'success',
            'business' => $business,
            'giveTrial' => $giveTrial
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