<?php

namespace App\Models;

use Auth;
use Image;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class PlaceToEatItem extends Model
{
    use HasFactory, SoftDeletes;

    public $table = "place_to_eat_items";

    public function placeToEat(){
        return $this->belongsTo('App\Models\PlaceToEat', 'place_to_eat_id', 'id');
    } 

    public function uploadMedia(Request $request){

        $success = true;
        $message = null;

        $validatedData = $request->validate([
            'image' => 'required|image|max:25000'
        ]);

        $user = Auth::user();
        
        $placeToEatItemsImagesPath = config('spotbie.place_to_eat_items_images_path');
        $hashedFileName = $validatedData['image']->hashName();

        $newFile = Image::make($request->file('image'))->resize(1200, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        
        $newFile = $newFile->encode('jpg', 60);
        $newFile = (string) $newFile;

        $imagePath = 'place-to-eat-items-media/images/' . $user->id. '/' . $hashedFileName;

        Storage::put($imagePath, $newFile);

        $imagePath = 'http://localhost:8000/' . $imagePath;

        $response = array(
            'success' => $success,
            'message' => $message,
            'image' => $imagePath
        );

        return response($response);

    }

    public function create(Request $request){

        $validatedData = $request->validate([
            'name' => 'required|string|max:75|min:1',
            'description' => 'required|string|max:350|min:1',
            'images' => 'nullable|string|max:500|min:1',
            'type' => 'required|numeric|max:6',
            'point_cost' => 'required|numeric|min:1'
        ]);

        $user = Auth::user();

        if($user){
            $placeToEat = $user->placeToEat;
        }

        $placeToEatReward = new PlaceToEatItem();

        $placeToEatReward->place_to_eat_id = $placeToEat->id;
        $placeToEatReward->name = $validatedData['name'];
        $placeToEatReward->description = $validatedData['description'];
        $placeToEatReward->images = (!is_null($validatedData['images'])) ? $validatedData['images'] : '0';
        $placeToEatReward->type = $validatedData['type'];
        $placeToEatReward->point_cost = $validatedData['point_cost'];

        DB::transaction(function () use ($placeToEatReward){
            $placeToEatReward->save();
        });  

        
        $response = array(
            'success' => true,
            'newPlaceToEat' => $placeToEat
        ); 

        return response($response);

    }

    public function updateReward(Request $request){

        $validatedData = $request->validate([
            'id' => 'required|numeric|min:1',
            'name' => 'required|string|max:75|min:1',
            'description' => 'required|string|max:350|min:1',
            'images' => 'nullable|string|max:500|min:1',
            'type' => 'required|numeric|max:6',
            'point_cost' => 'required|numeric|min:1'
        ]);

        $user = Auth::user();

        if($user){
            $placeToEat = $user->placeToEat;
        }

        $placeToEatReward = $placeToEat->placeToEatItems()->find($validatedData['id']);

        $placeToEatReward->place_to_eat_id = $placeToEat->id;
        $placeToEatReward->name = $validatedData['name'];
        $placeToEatReward->description = $validatedData['description'];
        $placeToEatReward->images = (!is_null($validatedData['images'])) ? $validatedData['images'] : '0';
        $placeToEatReward->type = $validatedData['type'];
        $placeToEatReward->point_cost = $validatedData['point_cost'];

        DB::transaction(function () use ($placeToEatReward){
            $placeToEatReward->save();
        });  
        
        $response = array(
            'success' => true,
            'newPlaceToEat' => $placeToEat
        ); 

        return response($response);
    }
    
    public function index(Request $request){

        $validatedData = $request->validate([
            'qrCodeLink' => 'string',
            'userHash' => 'string',
        ]);

        $user = Auth::user();

        $placeToEat = $user->placeToEat;

        $placeToEatItems = null;
        $loyalty_point_dollar_percent_value = null;

        if( isset($validatedData['qrCodeLink']) && isset($validatedData['userHash']) ){
            
            $businessUser = User::where('uuid', $validatedData['userHash'])->get()[0];

            $placeToEat = PlaceToEat::where('user_id', $businessUser->id)->get()[0]; 

            $businessMenu = PlaceToEatItem::select('*')
            ->where('place_to_eat_id', $placeToEat->id)
            ->get();

            $loyalty_point_dollar_percent_value = LoyaltyPointBalance::where('user_id', $placeToEat->id)
            ->get()[0]->loyalty_point_dollar_percent_value;

            if(!is_null($businessMenu))
                $placeToEatItems = $businessMenu;
            
        } else {
            $placeToEatItems = $placeToEat->placeToEatItems()->select('*')->get();
        }        

        $response = array(
            'success' => true,
            'placeToEatRewards' => $placeToEatItems,
            'placeToEatName' => $placeToEat->name,
            'loyalty_point_dollar_percent_value' => $loyalty_point_dollar_percent_value
        ); 

        return response($response);

    }

    public function deleteMe(Request $request){

        $validatedData = $request->validate([
            'id' => 'required|numeric'
        ]);

        $user = Auth::user();
        $reward_id = $validatedData['id'];

        if($user){
            
            DB::transaction(function () use ($user, $reward_id){
                PlaceToEatItem::where('id', $reward_id)->delete();
            });            

        }

        $response = array(
            'success' => true
        ); 

        return response($response);

    }

}
