<?php

namespace App\Models;

use Auth;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;

use App\Models\Business;
use App\Models\Reward;

use App\Helpers\UrlHelper;

class Ads extends Model
{

    use HasFactory, SoftDeletes; 

    public function business(){
        return $this->belongsTo('App\Models\Business', 'business_id', 'id');
    }

    public function nearbyBusiness($loc_x, $loc_y, $categories){

        return Business::select(
            'business.id', 'business.qr_code_link', 'business.name', 'business.address', 'business.categories', 'business.description', 
            'business.photo', 'business.qr_code_link', 'business.loc_x', 'business.loc_y',
            'spotbie_users.user_type',
            'loyalty_point_balances.balance', 'loyalty_point_balances.loyalty_point_dollar_percent_value',            
        )
        ->join('spotbie_users', 'business.id', '=', 'spotbie_users.id')
        ->join('loyalty_point_balances', function ($join){
            $join->on('business.id', '=', 'loyalty_point_balances.id')
            ->where('loyalty_point_balances.balance', '>', 0)
            ->where('loyalty_point_balances.loyalty_point_dollar_percent_value', '>', 0);
        })
        ->whereJsonContains('business.categories', $categories)
        ->whereRaw("( 
            (business.loc_x = $loc_x AND business.loc_y = $loc_y)
            OR (    
                    ABS ( 
                            SQRT    (   
                                        (POWER ( (business.loc_x - $loc_x), 2) ) +
                                        (POWER ( (business.loc_y - $loc_y), 2) ) 
                                    ) 
                        ) 
                    <= 0.06
                )
        )")
        ->has("rewards")  
        ->inRandomOrder()      
        ->limit(1)
        ->get()[0];

    }

    public function nearbyBusinessList($loc_x, $loc_y, $categories){

        return Business::select(
            'business.id', 'business.qr_code_link', 'business.name', 'business.address', 'business.categories', 'business.description', 
            'business.photo', 'business.qr_code_link', 'business.loc_x', 'business.loc_y',
            'spotbie_users.user_type',
            'loyalty_point_balances.balance', 'loyalty_point_balances.loyalty_point_dollar_percent_value',            
        )
        ->join('spotbie_users', 'business.id', '=', 'spotbie_users.id')
        ->join('rewards', 'business.id', '=', 'rewards.id')
        ->join('loyalty_point_balances', function ($join){
            $join->on('business.id', '=', 'loyalty_point_balances.id')
            ->where('loyalty_point_balances.balance', '>', 0)
            ->where('loyalty_point_balances.loyalty_point_dollar_percent_value', '>', 0);
        })
        ->whereJsonContains('business.categories', $categories)
        ->whereRaw("( 
            (business.loc_x = $loc_x AND business.loc_y = $loc_y)
            OR (    
                    ABS ( 
                            SQRT    (   
                                        (POWER ( (business.loc_x - $loc_x), 2) ) +
                                        (POWER ( (business.loc_y - $loc_y), 2) ) 
                                    ) 
                        ) 
                    <= 0.06
                )
        )")
        ->has("rewards")  
        ->inRandomOrder()      
        ->limit(5);

    }

    public function headerBanner(Request $request){

        $validatedData = $request->validate([
            'loc_x' => 'required|max:90|min:-90|numeric',
            'loc_y' => 'required|max:180|min:-180|numeric',
            'categories' => 'required|string|numeric',
        ]); 

        $loc_x = $validatedData['loc_x'];
        $loc_y = $validatedData['loc_y'];
        $categories = json_decode($validatedData['categories']);

        //Get a nearby business.
        $nearbyBusiness = $this->nearbyBusiness($loc_x, $loc_y, $categories);

        $ad = Ads::
        select('uuid', 'business_id', 'type', 'name', 'description', 'images', 'is_live')
        ->where('type', 0)
        ->where('business_id', '=', $nearbyBusiness->id)
        ->where('ends_at', '>', Carbon::now() )
        ->where('is_live', '=', true)
        ->orderBy('clicks', 'asc')
        ->limit(1)
        ->get()[0];

        $totalRewards = count(Reward::select('business_id')
        ->where('business_id', '=', $nearbyBusiness->id)
        ->get());

        $response = array(
            "success" => true,
            "business" => $nearbyBusiness,
            "ad" => $ad,
            "totalRewards" => $totalRewards
        );

        return response($response);

    }   

    public function singleAdList(Request $request){

        $validatedData = $request->validate([
            'loc_x' => 'required|max:90|min:-90|numeric',
            'loc_y' => 'required|max:180|min:-180|numeric',
            'categories' => 'required|string|numeric',
        ]); 

        $loc_x = $validatedData['loc_x'];
        $loc_y = $validatedData['loc_y'];
        $categories = json_decode($validatedData['categories']);

        //Get a nearby business.
        $nearbyBusiness = $this->nearbyBusiness($loc_x, $loc_y, $categories);

        $ad = Ads::
        select('uuid', 'business_id', 'type', 'name', 'description', 'images', 'is_live')
        ->where('type', 0)
        ->where('business_id', '=', $nearbyBusiness->id)
        ->where('ends_at', '>', Carbon::now() )
        ->where('is_live', '=', true)
        ->orderBy('clicks', 'asc')
        ->limit(1)
        ->get()[0];

        $totalRewards = count(Reward::select('business_id')
        ->where('business_id', '=', $nearbyBusiness->id)
        ->get());

        $response = array(
            "success" => true,
            "business" => $nearbyBusiness,
            "ad" => $ad,
            "totalRewards" => $totalRewards
        );

        return response($response);
        
    }

    public function nearbyCategoryAd(Request $request){

        $validatedData = $request->validate([
            'loc_x' => 'required|max:90|min:-90|numeric',
            'loc_y' => 'required|max:180|min:-180|numeric',
            'categories' => 'required|string',
        ]); 

        $loc_x = $validatedData['loc_x'];
        $loc_y = $validatedData['loc_y'];
        $categories = json_encode($validatedData['categories']);

        //Get a nearby business.
        $nearbyBusiness = $this->nearbyBusiness($loc_x, $loc_y, $categories);

        while($ad){

        }
        $ad = $this
        ->where('type', 1)
        ->inRandomOrder()
        ->limit(1)
        ->get()[0];

        $response = array(
            "success" => true,
            "ad" => $ad,
            "business" => $nearbyBusiness
        );

        return response($response);

    }   

    public function featuredNearbyAd(Request $request){

        $validatedData = $request->validate([
            'loc_x' => 'required|max:90|min:-90|numeric',
            'loc_y' => 'required|max:180|min:-180|numeric',
            'categories' => 'required|string',
        ]); 

        $loc_x = $validatedData['loc_x'];
        $loc_y = $validatedData['loc_y'];
        $categories = json_encode($validatedData['categories']);

        //Get a nearby business.
        $nearbyBusiness = $this->nearbyBusiness($loc_x, $loc_y, $categories);

        $ad = $this
        ->where('type', 2)
        ->inRandomOrder()
        ->limit(1)
        ->get()[0];

        $response = array(
            "success" => true,
            'business' => $nearbyBusiness,
            "ad" => $ad 
        );

        return response($response);

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

        $imagePath = '/rewards-media/images/' . $user->id. '/' . $hashedFileName;

        Storage::put($imagePath, $newFile);

        $imagePath = UrlHelper::getServerUrl() . $imagePath;

        $response = array(
            'success' => $success,
            'message' => $message,
            'image' => $imagePath
        );

        return response($response);

    }

    public function index(){

        $user = Auth::user();

        if(!$user){

            $response = array(
                "success" => false,
                "message" => "You are not authorized to view this content." 
            );
    
            return response($response); 

        } 

        $adList = $user->business->ads()->get();

        $response = array(
            "success" => true,
            "adList" => $adList 
        );

        return response($response);

    }

    public function create(Request $request){

    }

    public function updateModel(Request $request){

    }

    public function deleteModel(Request $request){

    }

}
