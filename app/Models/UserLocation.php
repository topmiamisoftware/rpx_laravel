<?php

namespace App\Models;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserLocation extends Model
{

    use HasFactory, SoftDeletes;

    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function spotbieUser(){
        return $this->belongsTo('App\Models\SpotbieUser', 'user_id', 'id');
    }

    public function getMyLocation(){

        $user = Auth::user();

        $location = $user->userLocation()->select('loc_x', 'loc_y');

        $response = array(
            'loc_x' => $location['loc_x'],
            'loc_y' => $location['loc_y']
        );

        return response($response);    

    }

    public function saveCurrentLocation(Request $request){

        $user = Auth::user();

        $validatedData = $request->validate([
            'loc_x' => 'required|max:90|min:-90|numeric',
            'loc_y' => 'required|max:180|min:-180|numeric',
        ]);

        $user->userLocation->loc_x = $validatedData['loc_x'];
        $user->userLocation->loc_y = $validatedData['loc_y'];

        if($user->userLocation->save()){
            $msg = "success";
        } else {
            $msg = "failed";
        }

        $response = array(
            'message' => $msg,
            'loc_x' => $user->userLocation->loc_x,
            'loc_y' => $user->userLocation->loc_y 

        ); 

        return response($response);

    }

    public function retrieveSurroundings(Request $request){       

        $validatedData = $request->validate([
            'search_type' => 'required|max:4|min:0|numeric',
            'loc_x' => 'required|max:90|min:-90|numeric',
            'loc_y' => 'required|max:180|min:-180|numeric'
        ]);

        $xr = '';
        
        $search_type = $validatedData['search_type'];
        $loc_x = $validatedData['loc_x'];
        $loc_y = $validatedData['loc_y'];

        if(Auth::check()){

            $user = Auth::user();

            switch($search_type){
                case "0":
                case "1":
                    $xr = "user_locations.user_id != '".$user['id']."' AND";
                    break;
                default:
                    $xr = "";                    
            }

        }
        
        $surrounding_object_list = DB::table('user_locations')
        ->join('spotbie_users', 'user_locations.user_id', 'spotbie_users.id')
        ->join('web_options', 'spotbie_users.id', 'web_options.user_id')
        ->join('users', 'spotbie_users.id', 'users.id')
        ->select('spotbie_users.default_picture', 'spotbie_users.description', 'spotbie_users.ghost_mode', 
        'web_options.bg_color', 'web_options.spotmee_bg', 'users.username')
        ->whereRaw("((($xr user_locations.user_id != 0 AND user_locations.loc_x = $loc_x AND user_locations.loc_y = $loc_y)
        OR ($xr user_locations.user_id != 0 
        AND (ABS(SQRT(((POWER((user_locations.loc_x - $loc_x), 2)) + (POWER ((user_locations.loc_y - $loc_y), 2))))) <= 0.01)))
        AND spotbie_users.user_type = '$search_type')")
        ->offset(0)
        ->limit(200)
        ->get();

        $response = array(
            'message' => 'success',
            'surrounding_object_list' => $surrounding_object_list
        );

        return response($response);    

    }

    public function createUserLocation(User $user){
        
        $userLocation = new UserLocation();

        $userLocation->user_id = $user->id;
        $userLocation->loc_x = 0;
        $userLocation->loc_y = 0;
        $userLocation->ip_address = 0;

        $userLocation->save();

    }

}
