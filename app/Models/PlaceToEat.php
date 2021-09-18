<?php

namespace App\Models;

use Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;

use App\Models\PlaceToEatItem;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlaceToEat extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'places_to_eat';

    public function placeToEatItems(){
        return $this->hasMany('App\Models\PlaceToEatItem', 'place_to_eat_id', 'id');
    }    

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    } 

    public function verify(Request $request){

        $validatedData = $request->validate([
            'name' => 'required|string|max:75|min:1',
            'description' => 'required|string|max:350|min:1',
            'address' => 'required|string|max:350|min:1',
            'photo' => 'required|string|max:650|min:1',
            'loc_x' => 'required|max:90|min:-90|numeric',
            'loc_y' => 'required|max:180|min:-180|numeric',
            'passkey' => 'required|string|max:6|min:6'
        ]);

        $spotbieBusinessPassKey = config('spotbie.business_pass_key');

        if($spotbieBusinessPassKey !== $validatedData['passkey']){
            $response = array(
                'message' => 'passkey_mismatch'
            ); 
            return response($response);
        }

        $user = Auth::user();

        $user->spotbieUser->user_type = 1;

        //check if the place to eat already exists.
        $existingPlaceToEat = $user->placeToEat;        

        if(!is_null($existingPlaceToEat)){
            $placeToEat = $user->placeToEat;       
        } else {
            $placeToEat = new PlaceToEat();
        }    

        $placeToEat->user_id = $user->id;
        $placeToEat->name = $validatedData['name'];
        $placeToEat->description = $validatedData['description'];
        $placeToEat->address = $validatedData['address'];
        $placeToEat->photo = $validatedData['photo'];
        $placeToEat->loc_x = $validatedData['loc_x'];
        $placeToEat->loc_y = $validatedData['loc_y'];
        $placeToEat->is_verified = 1;

        $placeToEat->qr_code_link = Str::uuid();

        if($existingPlaceToEat){
            DB::transaction(function () use ($placeToEat, $user){
                $user->placeToEat->save();
                $user->spotbieUser->save();
            });
        } else {
            DB::transaction(function () use ($placeToEat, $user){
                $placeToEat->save();
                $user->spotbieUser->save();
            });            
        }


        $response = array(
            'message' => 'success',
            'newPlaceToEat' => $placeToEat
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

}