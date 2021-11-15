<?php

namespace App\Models;

use Auth;
use Image;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use App\Models\Business;
use App\Helpers\UrlHelper;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;


class Reward extends Model
{
    use HasFactory, SoftDeletes;

    public $table = "rewards";

    public function business(){
        return $this->belongsTo('App\Models\Business', 'business_id', 'id');
    } 

    public function uploadMedia(Request $request){

        $success = true;
        $message = null;

        $validatedData = $request->validate([
            'image' => 'required|image|max:25000'
        ]);

        $user = Auth::user();
        
        $hashedFileName = $validatedData['image']->hashName();

        $newFile = Image::make($request->file('image'))->resize(1200, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        
        $newFile = $newFile->encode('jpg', 60);
        $newFile = (string) $newFile;

        $imagePath = 'rewards-media/images/' . $user->id. '/' . $hashedFileName;

        Storage::put($imagePath, $newFile, 'public');

        $imagePath = Storage::url($imagePath);

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
            $business = $user->business;
        }

        $businessReward = new Reward();

        $businessReward->uuid = Str::uuid();

        $businessReward->business_id = $business->id;
        $businessReward->name = $validatedData['name'];
        $businessReward->description = $validatedData['description'];
        $businessReward->images = (!is_null($validatedData['images'])) ? $validatedData['images'] : '0';
        $businessReward->type = $validatedData['type'];
        $businessReward->point_cost = $validatedData['point_cost'];

        DB::transaction(function () use ($businessReward){
            $businessReward->save();
        }, 3);  

        
        $response = array(
            'success' => true,
            'newBusiness' => $business
        ); 

        return response($response);

    }

    public function claim(Request $request){

        $validatedData = $request->validate([
            'redeemableHash' => 'required|string|max:36'
        ]);

        $user = Auth::user();

        $success = false;
        $reward = null;

        if($user)
        {
            $reward = Reward::where('uuid', $validatedData['redeemableHash'])->get()->first();

            if($reward)
            {            
                $redeemed = new RedeemableItems();
                $redeemed->uuid = Str::uuid();
                $redeemed->business_id = $reward->business_id;
                $redeemed->redeemer_id = $user->id;
                $redeemed->total_spent = $reward->point_cost;
                $redeemed->redeemed = true;
                $redeemed->reward_id = $reward->id;

                $redeemed->dollar_value = floatval($reward->point_cost * ($user->loyaltyPointBalance->loyalty_point_dollar_percent_value / 100) );
                
                //Charge the user the LP Cost.
                $user->loyaltyPointBalance->balance = $user->loyaltyPointBalance->balance - $reward->point_cost;

                DB::transaction(function () use ($user, $redeemed){
                    $redeemed->save();
                    $user->loyaltyPointBalance->save();                    
                });                                
            }
            $success = true;
        }

        $response = response([
            "success" => $success,
            "reward" => $reward,
            "loyalty_points" => $user->loyaltyPointBalance
        ]);

        return $response;

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
            $business = $user->business;
        }

        $businessReward = $business->rewards()->find($validatedData['id']);
        
        $businessReward->business_id = $business->id;
        $businessReward->name = $validatedData['name'];
        $businessReward->description = $validatedData['description'];
        $businessReward->images = (!is_null($validatedData['images'])) ? $validatedData['images'] : '0';
        $businessReward->type = $validatedData['type'];
        $businessReward->point_cost = $validatedData['point_cost'];

        DB::transaction(function () use ($businessReward){
            $businessReward->save();
        }, 3);  
        
        $response = array(
            'success' => true,
            'newBusiness' => $business
        ); 

        return response($response);
    }
    
    public function index(Request $request){

        $validatedData = $request->validate([
            'qrCodeLink' => ['nullable', 'string']
        ]);

        $rewards = null;
        $loyalty_point_dollar_percent_value = null;

        if( isset($validatedData['qrCodeLink']) ){
            
            $business = Business::                    
            select('id', 'name', 'description', 'address', 'qr_code_link', 'loc_x', 'loc_y', 'is_verified', 'categories', 'updated_at')
            ->where('qr_code_link', $validatedData['qrCodeLink'])
            ->get()[0]; 

            $businessMenu = Reward::select('*')
            ->where('business_id', $business->id)
            ->get();

            $loyalty_point_dollar_percent_value = LoyaltyPointBalance::where('id', $business->id)
            ->get()[0]->loyalty_point_dollar_percent_value;

            if( !is_null($businessMenu) )
                $rewards = $businessMenu;
            
        } else {

            $user = Auth::user();

            $business = $user->business;

            if($business->first())
                $rewards = $business->rewards()->select('*')->get();
            else 
                $rewards = [];

        }        

        $response = array(
            'success' => true,
            'rewards' => $rewards,
            'business' => $business,
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
                Reward::where('id', $reward_id)->delete();
            }, 3);            

        }

        $response = array(
            'success' => true
        ); 

        return response($response);

    }

}
