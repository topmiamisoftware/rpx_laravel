<?php

namespace App\Models;

use Auth;
use Image;

use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;

use App\Models\Business;
use App\Models\Reward;

use App\Helpers\UrlHelper;

use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Subscription;

use Illuminate\Support\Str;

class Ads extends Model
{
    
    use HasFactory, SoftDeletes; 

    protected $fillable = ['business_id'];

    public $table = "ads";

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
            'loc_x' => 'max:90|min:-90|numeric',
            'loc_y' => 'max:180|min:-180|numeric',
            'categories' => 'string|numeric',
            'id' => 'nullable|numeric',
        ]); 

        if(isset($validatedData['id'])){

            $ad = Ads::find($validatedData['id']);

            $business = Business::find($ad->business_id);

            $totalRewards = count(Reward::select('business_id')
            ->where('business_id', '=', $business->id)
            ->get());
            
            //Add click to ad.
            DB::transaction(function () use ($ad) {
                
                $ad->clicks++;

                $ad->save();

            });

            $response = array(
                "success" => true,
                "business" => $business,
                "ad" => $ad,
                "totalRewards" => $totalRewards
            );
    
            return response($response);

        }

        $loc_x = $validatedData['loc_x'];
        $loc_y = $validatedData['loc_y'];
        $categories = json_decode($validatedData['categories']);

        //Get a nearby business.
        $nearbyBusiness = $this->nearbyBusiness($loc_x, $loc_y, $categories);

        $ad = Ads::
        select('uuid', 'business_id', 'type', 'name', 'description', 'images')
        ->where('type', 0)
        ->where('business_id', '=', $nearbyBusiness->id)
        ->where('is_live', '=', 1)
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

    public function getByUuid(Request $request){

        $validatedData = $request->validate([   
            'uuid' => 'required|string'
        ]);
        
        $ad = Ads::select('*')
        ->where('uuid', '=', $validatedData['uuid'])
        ->first();

        $business = Business::where('id', '=', $ad->business_id)
        ->first();

        $response = array(
            "success" => true,
            "business" => $business,
            "ad" => $ad,
        );

        return response($response);

    }

    public function singleAdList(Request $request){

        $validatedData = $request->validate([            
            'loc_x' => 'max:90|min:-90|numeric',
            'loc_y' => 'max:180|min:-180|numeric',
            'categories' => 'string|numeric',
            'id' => 'nullable|numeric'
        ]); 

        if(isset($validatedData['id'])){

            $ad = Ads::find($validatedData['id']);

            $business = Business::find($ad->business_id);

            $totalRewards = count(Reward::select('business_id')
            ->where('business_id', '=', $business->id)
            ->get());
    
            $response = array(
                "success" => true,
                "business" => $business,
                "ad" => $ad,
                "totalRewards" => $totalRewards
            );
    
            return response($response);

        }

        $loc_x = $validatedData['loc_x'];
        $loc_y = $validatedData['loc_y'];
        $categories = json_decode($validatedData['categories']);

        //Get a nearby business.
        $nearbyBusiness = $this->nearbyBusiness($loc_x, $loc_y, $categories);
        
        $ad = Ads::
        select('uuid', 'business_id', 'type', 'name', 'description', 'images')
        ->where('type', 0)
        ->where('business_id', '=', $nearbyBusiness->id)
        ->where('is_live', '=', 1)
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

    public function featuredAdList(Request $request){

        $validatedData = $request->validate([
            'loc_x' => 'max:90|min:-90|numeric',
            'loc_y' => 'max:180|min:-180|numeric',
            'categories' => 'string',
            'id' => 'nullable|numeric'
        ]); 

        if(isset($validatedData['id'])){

            $ad = Ads::find($validatedData['id']);

            $business = Business::find($ad->business_id);

            $totalRewards = count(Reward::select('business_id')
            ->where('business_id', '=', $business->id)
            ->get());
    
            $response = array(
                "success" => true,
                "business" => $business,
                "ad" => $ad,
                "totalRewards" => $totalRewards
            );
    
            return response($response);

        }

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

        $imagePath = '/ad-media/images/' . $user->id. '/' . $hashedFileName;

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

        $validatedData = $request->validate([
            'name' => 'required|string|max:75|min:1',
            'description' => 'required|string|max:350|min:1',
            'images' => 'required|string|max:500|min:1',
            'type' => 'required|numeric|max:6'
        ]);

        $user = Auth::user();

        if($user) $business = $user->business;
        
        $businessAd = new Ads();

        $businessAd->uuid = Str::uuid();

        $businessAd->business_id = $business->id;

        $businessAd->name = $validatedData['name'];
        $businessAd->description = $validatedData['description'];
        $businessAd->images = $validatedData['images'];
        $businessAd->type = $validatedData['type'];
        $businessAd->is_live = false;

        switch($businessAd->type){
            case 0:
                $businessAd->dollar_cost = 15.99;
                break;
            case 1:
                $businessAd->dollar_cost = 13.99;
                break;
            case 2:
                $businessAd->dollar_cost = 10.99;
                break;                                    
        }

        DB::transaction(function () use ($businessAd){

            $businessAd->save();

        });  
        
        $newAd = $businessAd->refresh();

        $response = array(
            'success' => true,
            'newAd' => $newAd,
        ); 

        return response($response);

    }

    public function savePayment(Request $request){
        
        $validatedData = $request->validate([
            "ad" => [
                "id" => ['required', 'string']
            ],
            "payment_method" => [
                "id" => ['required', 'string']
            ]
        ]);

        $adId = $validatedData['ad']['id'];
        $paymentMethodId = $validatedData['payment_method']['id'];

        $adSubscription = Ads::where('id', '=', $adId)
        ->first();

        $price_name = config('spotbie.header_banner_price');

        switch($adSubscription->type){
            case 0:
                $price_name = ["price" => config('spotbie.header_banner_price')];
                break;
            case 1:
                $price_name = ["price" => config('spotbie.featured_related_price')];
                break;
            case 2:
                $price_name = [ "price" => config('spotbie.footer_banner_price')];
                break;
        }

        if($adSubscription !== null){

            $userId = $adSubscription->business_id;

            $userStripeId = User::find($userId)->stripe_id;
            
            $user = Cashier::findBillable($userStripeId);

            $user->updateDefaultPaymentMethod($paymentMethodId);

            //Update the subscription if the user chose a different ad type.
            $existingSubscription = $user->subscriptions()->where('name', '=', $adId)->first();

            if($existingSubscription !== null ){          

                $user->subscription($existingSubscription->name)->swapAndInvoice($price_name);
                
            } else {

                //Create the subscription with the payment method provided by the user.
                $user->newSubscription($adSubscription->id, [$price_name] )->create($paymentMethodId);

            }

            $newSubscription = $user->subscriptions()->where('name', '=', $adId)->first();

            DB::transaction(function () use ($adSubscription, $newSubscription){

                $adSubscription->subscription_id = $newSubscription->id;
                $adSubscription->is_live = 1;

                $adSubscription->save();

            });  

        }

        $businessAd = $adSubscription->refresh();

        $response = array(
            'success' => true,
            'newAd' => $businessAd,
            'user' => $user
        ); 

        return response($response);

    }

    public function updateModel(Request $request){

        $validatedData = $request->validate([
            'id' => 'required|numeric',
            'name' => 'required|string|max:75|min:1',
            'description' => 'required|string|max:350|min:1',
            'images' => 'required|string|max:500|min:1',
            'type' => 'required|numeric|max:6'
        ]);

        $user = Auth::user();

        $userBillable = Cashier::findBillable($user->stripe_id);

        if($user) $business = $user->business;

        $businessAd = $business->ads()->find($validatedData['id']);
        
        $businessAd->business_id = $business->id;
        $businessAd->name = $validatedData['name'];
        $businessAd->description = $validatedData['description'];
        
        $businessAd->images = $validatedData['images'];

        $businessAd->type = $validatedData['type'];

        $price_name = config('spotbie.header_banner_prod');

        switch($businessAd->type){
            case 0:
                $businessAd->dollar_cost = 15.99;
                break;
            case 1:
                $businessAd->dollar_cost = 13.99;
                break;
            case 2:
                $businessAd->dollar_cost = 10.99;
                break;                                    
        }

        DB::transaction(function () use ($businessAd){

            $businessAd->save();

        });  
        
        $response = array(
            'success' => true,
            'newAd' => $businessAd
        ); 

        return response($response);
    }

    public function deleteModel(Request $request){

        $validatedData = $request->validate([
            'id' => 'required|numeric'
        ]); 

        $user = Auth::user();

        $adToDelete = $validatedData['id']; 

        if($user){
            
            $userBillable = Cashier::findBillable($user->stripe_id);

            $userBillable->subscription($adToDelete)->cancelNow();

            DB::transaction(function () use ($user, $adToDelete){

                Ads::where('id', $adToDelete)
                ->update([
                    "is_live" => 0
                ]);
                
                Ads::where('id', $adToDelete)->delete();
            
            });            

        }

        $response = array(
            'success' => true
        ); 

        return response($response);

    }

}
