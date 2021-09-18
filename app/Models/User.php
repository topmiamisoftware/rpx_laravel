<?php

namespace App\Models;

use Auth;
use Mail;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as IlluminatePassword;
use Illuminate\Support\Str;

use Tymon\JWTAuth\Contracts\JWTSubject;

use App\Rules\FirstName;
use App\Rules\LastName;
use App\Rules\Password;
use App\Rules\Username;

class User extends Authenticatable implements JWTSubject
{
    
    use Notifiable, HasFactory, SoftDeletes;  

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    public function userLocation(){
        return $this->hasOne('App\Models\UserLocation');
    }

    public function streamPosts(){
        return $this->hasMany('App\Models\StreamPost');
    }

    public function webOptions(){
        return $this->hasOne('App\Models\WebOptions');
    }

    public function contactMe(){
        return $this->hasOne('App\Models\ContactMe');
    }

    public function placeToEat(){
        return $this->hasOne('App\Models\PlaceToEat', 'user_id');
    }

    public function spotbieUser(){        
        return $this->hasOne('App\Models\SpotbieUser', 'id');
    }

    public function facebookUser(){        
        return $this->hasOne('App\Models\FacebookUser', 'id');
    }

    public function googleUser(){        
        return $this->hasOne('App\Models\GoogleUser', 'id');
    }

    public function relationships(){                
        return $this->hasMany('App\Models\Friendship');
    }    

    public function albums(){
        return $this->hasMany('App\Models\Album');
    }

    public function defaultImages(){
        return $this->hasMany('App\Models\DefaultImages');
    }

    public function myFavorites(){
        return $this->hasMany('App\Models\MyFavorites', 'user_id');
    }

    public function loyaltyPointBalance(){
        return $this->hasOne('App\Models\LoyaltyPointBalance', 'user_id');
    }

    public function signUp(Request $request){

        $validatedData = $request->validate([
            'username' => ['required', 'unique:users', 'max:35', 'min:1', new Username],
            'email' => ['required', 'unique:users', 'email'],
            'password' => ['required', new Password]
        ]);
        
        $user = new User();
        
        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($validatedData['password']);
        
        $newSpotbieUser = new SpotbieUser();
        
        $newSpotbieUser->first_name = null;
        $newSpotbieUser->last_name = null;

        $fullName = $validatedData['first_name'] . ' ' . $validatedData['last_name'];
        $description = "Hello my name is $fullName. Welcome to my Spotbie profile."; 

        $newSpotbieUser->description = $description;
        $newSpotbieUser->last_known_ip_address = $request->ip;

        DB::transaction(function () use ($user, $newSpotbieUser){
            $user->save();
            $newSpotbieUser->id = $user->id;
            $newSpotbieUser->save();

            $loyaltyPointBalance = new LoyaltyPointBalance();
            $loyaltyPointBalance->user_id = $user->id;
            $loyaltyPointBalance->balance = 1000;//One key can make the difference.

            $loyaltyPointBalance->save();

        });

        //Start the session
        Auth::login($user);

        $this->sendConfirmationEmail();
        
        $user = Auth::user();
        $user = $user
        ->select('id', 'username', 'email')
        ->where('id', $user->id)
        ->first();

        $signUpResponse = array(
            'tokenInfo' => $this->respondWithToken(Auth::refresh()),
            'message' => 'success',
            'user' => $user,
            'spotbie_user' => $newSpotbieUser
        );

        return response($signUpResponse);

    }

    public function logIn(Request $request){

        $validatedData = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', new Password],
            'timezone' => ['required', 'string'],
            'remember_me_opt' => ['required', 'string']
        ]);        

        $login = $validatedData['login'];
        $password = $validatedData['password'];
        $timezone = $validatedData['timezone'];
        $remember_me = $validatedData['remember_me_opt'];

        $login_failed = true;
        
        $searchUser = User::onlyTrashed()
        ->select('id', 'password')
        ->where(
            function($query) use ($login){
                $query->where('username', $login)
                    ->orWhere('email', $login);
            }
        )
        ->first();
            
        if($searchUser !== null && Hash::check($password, $searchUser->password)) 
            $searchUser->restore();
        
        $checkForSocialNetworkAccount = User::select('id')
        ->where(
            function($query) use ($login){
                $query->where('username', $login)
                    ->orWhere('email', $login);
            }
        )
        ->where('password', null)
        ->first();
        
        if($checkForSocialNetworkAccount){

            $googleUserExists = GoogleUser::select('user_id')->where('user_id', $checkForSocialNetworkAccount->id)->first();       

            if($googleUserExists){
                $loginResponse = array(
                    'message' => 'spotbie_google_account'
                );
                return response($loginResponse);
            }
            
            $facebookUserExists = FacebookUser::select('user_id')->where('user_id', $checkForSocialNetworkAccount->id)->first();
            if($facebookUserExists){
                $loginResponse = array(
                    'message' => 'spotbie_fb_account'
                );
                return response($loginResponse);
            }

        }

        if
        ( 
            !Auth::attempt(['email' => $login, 'password' => $password]) &&
            !Auth::attempt(['username' => $login, 'password' => $password])
        )
            $login_failed = true;
        else
            $login_failed = false;

        
        if($login_failed){

            $loginResponse = array(
                'message' => 'invalid_cred'
            );      

        } else {

            $user = User::select('id', 'username')->where(
                function($query) use ($login){
                    $query->where('username', $login)
                        ->orWhere('email', $login);
                }
            )
            ->first();

            $spotbieUser = $user->spotbieUser()->select('default_picture')->first(); 

            //Start the session
            Auth::login($user, $remember_me);
            $token = Auth::refresh();
            
            if($remember_me == '1'){
                Auth::user()->remember_token = $token;
            } else {
                Auth::user()->remember_token = null;
            }

            Auth::user()->save();

            $loginResponse = array(
                'token_info' => $this->respondWithToken($token),
                'message' => 'success',
                'user' => $user,
                'spotbie_user' => $spotbieUser
            );   

        }

        return response($loginResponse);

    }
    
    public function facebookLogin(Request $request){

        $validatedData = $request->validate([
            'userID' => ['required', 'string'],
            'firstName' => ['required', new FirstName],
            'lastName' => ['required', new LastName],
            'email' => ['required', 'email'],
            'photoUrl' => ['required', 'string'],
            'remember_me' => ['required', 'string']
        ]);
        
        //Let's deny user the FbLogin if their FB email is already in use with our system.
        $userEmailInUse = $this->select('id', 'email')->where('email', $validatedData['email'])->first();

        if($userEmailInUse){
            
            $googleUserExists = GoogleUser::select('user_id')->where('user_id', $userEmailInUse->id)->first();

            if($googleUserExists){
                $loginResponse = array(
                    'message' => 'spotbie_google_account'
                ); 
                return response($loginResponse);
            } 
                        
            $facebookUserExists = FacebookUser::select('user_id')->where('user_id', $userEmailInUse->id)->first();

            if(!$facebookUserExists){
                $loginResponse = array(
                    'message' => 'spotbie_account'
                ); 
    
                return response($loginResponse);
            }

        }   

        //Let's login with facebook and store the user's facebook information in DB
        $fbUser = FacebookUser::select('user_id')->where('facebook_user_id', $validatedData['userID'])->first();

        //Check if user already exists
        if($fbUser){

            $user_id = $fbUser->user_id;
            $user = $this->select('id', 'username')->where('id', $user_id)->first();

            //If user exists, let's update their facebook information and log them in to SpotBie
            if($user){
                
                $user->email = $validatedData['email'];

                $spotbieUser = $user->spotbieUser;
                
                $spotbieUser->first_name = $validatedData['firstName'];
                $spotbieUser->last_name = $validatedData['lastName'];
                
                $spotbieUser->default_picture = $validatedData['photoUrl'];

                $fullName = $validatedData['firstName'] . ' ' . $validatedData['lastName'];

                $spotbieUser->last_known_ip_address = $request->ip;

                DB::transaction(function () use ($user, $spotbieUser){
                    $user->save();
                    $spotbieUser->save();
                });

                $remember_me = $validatedData['remember_me'];

                //Start the session
                Auth::login($user, $remember_me);
                $token = Auth::refresh();
                
                if($remember_me == '1'){
                    Auth::user()->remember_token = $token;
                } else {
                    Auth::user()->remember_token = null;
                }

                Auth::user()->save();

                $loginResponse = array(
                    'token_info' => $this->respondWithToken($token),
                    'message' => 'success',
                    'user' => $user,
                    'spotbie_user' => $spotbieUser
                );   

            } else {
                
                $loginResponse = array(
                    'message' => 'really_messed_up_error'
                );    

            }
            
            
        } else {

            //If user doesn't exists, let's create their facebook and spotbie account, then log them in.
            $user = new User();
            
            $user->username = $validatedData['firstName'] . "." . $validatedData['lastName'] . "." . $validatedData["userID"];
            $user->email = $validatedData['email'];
            $user->password = null;
            
            $newSpotbieUser = new SpotbieUser();
            
            $newSpotbieUser->first_name = $validatedData['firstName'];
            $newSpotbieUser->last_name = $validatedData['lastName'];

            $newSpotbieUser->default_picture = $validatedData['photoUrl'];

            $fullName = $validatedData['firstName'] . ' ' . $validatedData['lastName'];
            $description = "Hello my name is $fullName. Welcome to my Spotbie profile."; 

            $newSpotbieUser->description = $description;
            $newSpotbieUser->last_known_ip_address = $request->ip;            

            DB::transaction(function () use ($user, $newSpotbieUser, $validatedData){

                $user->save();
                $user->username = $newSpotbieUser->first_name . "." . $newSpotbieUser->last_name . "." . $user->id;
                $user->save();

                $newSpotbieUser->id = $user->id;
                $newSpotbieUser->save();

                $fbUser = new FacebookUser();

                $fbUser->facebook_user_id = $validatedData["userID"];
                $fbUser->user_id = $user->id;

                $LoyaltyPointBalance = new LoyaltyPointBalance();
                $LoyaltyPointBalance->user_id = $user->id;
                $LoyaltyPointBalance->balance = 0;

                $LoyaltyPointBalance->save();

                $fbUser->save();

            });            

            $newSpotbieUser = $user->spotbieUser;

            //Start the session
            Auth::login($user, $remember_me);
            $token = Auth::refresh();
            
            $this->sendConfirmationEmail();

            if($remember_me == '1'){
                Auth::user()->remember_token = $token;
            } else {
                Auth::user()->remember_token = null;
            }

            Auth::user()->save();

            $loginResponse = array(
                'token_info' => $this->respondWithToken($token),
                'message' => 'success',
                'user' => $user,
                'spotbie_user' => $newSpotbieUser
            );    

        }

        return response($loginResponse);

    }

    public function googleLogin(Request $request){
        
        //Let's login with google and store the user's google information in DB 

        $validatedData = $request->validate([
            'userID' => ['required', 'string'],
            'firstName' => ['required', new FirstName],
            'lastName' => ['required', new LastName],
            'email' => ['required', 'email'],
            'photoUrl' => ['required', 'string'],
            'remember_me' => ['required', 'string']
        ]);
        
        //Let's deny user the FbLogin if their FB email is already in use with our system.
        $userEmailInUse = $this->select('id', 'email')->where('email', $validatedData['email'])->first();
        
        if($userEmailInUse){
            
            $facebookUserExists = FacebookUser::select('user_id')->where('user_id', $userEmailInUse->id)->first();

            if($facebookUserExists){
                $loginResponse = array(
                    'message' => 'spotbie_fb_account'
                ); 
    
                return response($loginResponse);
            }

            $googleUserExists = GoogleUser::select('user_id')->where('user_id', $userEmailInUse->id)->first();

            if(!$googleUserExists){
                $loginResponse = array(
                    'message' => 'spotbie_account'
                ); 
    
                return response($loginResponse);
            }

        }

        //Let's login with google and store the user's facebook information in DB
        $googleUser = GoogleUser::select('user_id')->where('google_user_id', $validatedData['userID'])->first();

        //Check if user already exists
        if($googleUser){

            $user_id = $googleUser->user_id;
            $user = $this->select('id', 'username')->where('id', $user_id)->first();

            //If user exists, let's update their facebook information and log them in to SpotBie
            if($user){
                
                $user->email = $validatedData['email'];

                $spotbieUser = $user->spotbieUser;
                
                $spotbieUser->first_name = $validatedData['firstName'];
                $spotbieUser->last_name = $validatedData['lastName'];
                
                $spotbieUser->default_picture = $validatedData['photoUrl'];

                $fullName = $validatedData['firstName'] . ' ' . $validatedData['lastName'];

                $spotbieUser->last_known_ip_address = $request->ip;

                DB::transaction(function () use ($user, $spotbieUser){
                    $user->save();
                    $spotbieUser->save();
                });

                $remember_me = $validatedData['remember_me'];

                //Start the session
                Auth::login($user, $remember_me);
                $token = Auth::refresh();
                
                if($remember_me == '1'){
                    Auth::user()->remember_token = $token;
                } else {
                    Auth::user()->remember_token = null;
                }

                Auth::user()->save();

                $loginResponse = array(
                    'token_info' => $this->respondWithToken($token),
                    'message' => 'success',
                    'user' => $user,
                    'spotbie_user' => $spotbieUser
                );   

            } else {
                
                $loginResponse = array(
                    'message' => 'really_messed_up_error'
                );    

            }
            
            
        } else {

            //If user doesn't exists, let's create their facebook and spotbie account, then log them in.
            $user = new User();
            
            $user->username = $validatedData['firstName'] . "." . $validatedData['lastName'] . "." . $validatedData["userID"];
            $user->email = $validatedData['email'];
            $user->password = null;
            
            $newSpotbieUser = new SpotbieUser();
            
            $newSpotbieUser->first_name = $validatedData['firstName'];
            $newSpotbieUser->last_name = $validatedData['lastName'];

            $newSpotbieUser->default_picture = $validatedData['photoUrl'];

            $fullName = $validatedData['firstName'] . ' ' . $validatedData['lastName'];
            $description = "Hello my name is $fullName. Welcome to my Spotbie profile."; 

            $newSpotbieUser->description = $description;
            $newSpotbieUser->last_known_ip_address = $request->ip;            

            DB::transaction(function () use ($user, $newSpotbieUser, $validatedData){

                $user->save();
                $user->username = $newSpotbieUser->first_name . "." . $newSpotbieUser->last_name . "." . $user->id;
                $user->save();

                $newSpotbieUser->id = $user->id;
                $newSpotbieUser->save();

                $googleUser = new GoogleUser();

                $googleUser->google_user_id = $validatedData["userID"];
                $googleUser->user_id = $user->id;

                $LoyaltyPointBalance = new LoyaltyPointBalance();
                $LoyaltyPointBalance->user_id = $user->id;
                $LoyaltyPointBalance->balance = 0;

                $LoyaltyPointBalance->save();
                $googleUser->save();

            });

            $newSpotbieUser = $user->spotbieUser;

            $remember_me = $validatedData['remember_me'];            

            //Start the session
            Auth::login($user, $remember_me);
            $token = Auth::refresh();
            
            $this->sendConfirmationEmail();

            if($remember_me == '1'){
                Auth::user()->remember_token = $token;
            } else {
                Auth::user()->remember_token = null;
            }

            Auth::user()->save();

            $loginResponse = array(
                'token_info' => $this->respondWithToken($token),
                'message' => 'success',
                'user' => $user,
                'spotbie_user' => $newSpotbieUser
            );    

        }

        return response($loginResponse);

    }

    public function logOut(Request $request){

        Auth::logout();

        $logoutResponse = array(
            'success' => true
        );   

        return response($logoutResponse);

    }

    public function closeBrowser(Request $request){

        if(Auth::user()->remember_me !== NULL)
            Auth::logout();        

        $logoutResponse = array(
            'success' => true
        );   

        return response($logoutResponse);

    }

    public function checkIfLoggedIn(){

        if(Auth::check()){

            $msg = '1';
            $userId = Auth::user()->id;

        } else {
            $msg = 'not_logged_in';
            $userId = null;
        }

        $response = array(
            'message' => $msg,
            'user_id' => $userId
        );

        return response($response);

    }

    public function getMyStream(Request $request){
        
        $user = Auth::user();

        $stream_posts = $user->streamPosts;

        $response = array(
            'message' => $stream_posts
        );

        return response($response);

    }

    private function sendConfirmationEmail (){

        $user = Auth::user();
        $spotbieUser = $user->spotbieUser()->first();

        Mail::send('emails.account_created', ['user' => $user, 'spotbieUser' => $spotbieUser], function ($m) use ($user) {

            $m->from('welcome@spotbie.com', 'SpotBie.com');
            $m->to($user->email, $user->username)->subject('Welcome to SpotBie!');
        
        });

    }

    public function getSettings(){
        
        $user = Auth::user();

        $userSettings = array(
            "hash" => $user->uuid,
            "username" => $user->username,
            "email" => $user->email
        );

        $spotbieUserSettings = $user
        ->spotbieUser()
        ->select('user_type', 'first_name', 'last_name', 'animal', 'ghost_mode', 'privacy', 'phone_number')
        ->get()[0];

        $placeToEat = $user
        ->placeToEat()
        ->select('id', 'user_id', 'name', 'description', 'address', 'photo', 'is_verified', 'qr_code_link', 'loc_x', 'loc_y', 'created_at', 'updated_at')
        ->get();

        if(count($placeToEat) > 0) 
            $placeToEat = $placeToEat[0];
        else
            $placeToEat = null;

        $settingsResponse = array(
            'message' => 'success',
            'user' => $userSettings,
            'spotbie_user' => $spotbieUserSettings,
            'place_to_eat' => $placeToEat
        );

        return response($settingsResponse);

    }
    
    public function saveSettings(Request $request){
        
        $user = Auth::user();

        if($user->username === $request->username) {

            $validatedData = $request->validate([
                'username' => 'required|string|max:35|min:1',
                'email' => 'required|email',
                'first_name' => ['required', new FirstName],
                'last_name' => ['required', new LastName],
                'ghost_mode' => 'boolean',
                'privacy' => 'boolean',
                'account_type' => 'required|numeric',
                'phone_number' => 'string|max:35|nullable'
            ]);

        } else {

            $validatedData = $request->validate([
                'username' => 'required|string|unique:users|max:35|min:1',
                'email' => 'required|email',
                'first_name' => ['required', new FirstName],
                'last_name' => ['required', new LastName],
                'ghost_mode' => 'boolean',
                'privacy' => 'boolean',
                'account_type' => 'required|numeric',
                'phone_number' => 'string|max:35|nullable'
            ]);

        }

        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];

        $user->spotbieUser->first_name = $validatedData['first_name'];
        $user->spotbieUser->last_name = $validatedData['last_name'];

        $user->spotbieUser->phone_number = $validatedData['phone_number'];

        $user->spotbieUser->user_type = $validatedData['account_type'];

        if($validatedData['account_type'] === 4){
            $user->spotbieUser->ghost_mode = $validatedData['ghost_mode'];
            $user->spotbieUser->privacy = $validatedData['privacy'];
        }
        
        DB::transaction(function () use ($user){
            $user->save();
            $user->spotbieUser->save();
        });

        $response = array(
            'success' => true,
            'user' => $user
        );

        return response($response);

    }

    public function confirmAccount(Request $request){

    }

    public function savePassword(Request $request){

        $validatedData = $request->validate([
            'password' => ['required', new Password, 'confirmed']
        ]);

        $user = Auth::user();

        $user->password = Hash::make($validatedData['password']);
        
        $user->save();

        $response = array(
            'message' => 'success'
        );

        return $response;

    }

    public function getUser(Request $request){

        $user = $this->only('id', 'username');
        $spotbieUser = $this->spotbieUser()->select('first_name', 'last_name', 'description', 'default_picture')->first();

        $webOptions = $this->webOptions()->select('bg_color', 'spotmee_bg', 'time_zone')->first();
        $defaultImages = $this->defaultImages()->select('default_image_url')->get();

        $response = array(
            'message' => 'success',
            'user' => $user,
            'spotbie_user' => $spotbieUser,
            'web_options' => $webOptions,
            'default_images' => $defaultImages
        );

        return response($response);

    }

    public function setPassResetPin(Request $request){

        $success = false;

        $validatedData = $request->validate([
            'email' => 'required|string'
        ]);
        
        $user = User::select('id', 'email')
        ->where('email', $validatedData['email'])
        ->first();

        if($user === null){

            $user = SpotbieUser::select('id')
            ->where('email', $validatedData['email'])
            ->first();

            $user = User::select('id', 'email')
            ->where('id', $user->id)
            ->first();
            
        }

        $spotbieUser = SpotbieUser::select('id', 'email')
        ->where('id', $user->id)
        ->first();

        $userId = $user->id;

        $status = IlluminatePassword::sendResetLink(
            $user->only('email')
        );

        $success = true;

        $response = array(
            'success' => $success,
            'user' => $user,
            'status' => $status
        );

        return response($response);

    }

    public function completePassReset(Request $request){

        $success = true;

        $validatedData = $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', new Password, 'confirmed']
        ]);
        
        $status = IlluminatePassword::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
    
                $user->setRememberToken(Str::random(60));
    
                event(new IlluminatePassword($user));
            }
        );

        $response = array(
            'success' => $success,
            'status' => $status
        );

        return response($response);

    }

    public function changePassword(Request $request){

        $success = false;

        $user = Auth::user();

        $validatedData = $request->validate([
            'password' => ['required', new Password, 'confirmed'],
            'current_password' => ['required', new Password]
        ]);
            
        if(Hash::check($validatedData['current_password'], $user->password)){
            $user->password = Hash::make($validatedData['password']);
            $user->save();
            $success = true;
            $message = 'saved';
        } else {
            $message = 'SB-E-000';
        }

        $response = array(
            'success' => $success,
            'message' => $message,
        );

        return response($response);

    }

    public function deactivate(Request $request){

        $success = false;

        $user = Auth::user();

        $validatedData = $request->validate([
            'password' => ['required', new Password]
        ]);            

        if($user->delete()) 
            $success = true;
        else 
            $success = false;
        
        $response = array(
            'success' => $success
        );

        return response($response);

    }

    public function activate(){
        
    }

    public function uniqueEmail(Request $request){

        $emailConfirmed = EmailConfirmation::select(
            'email', 'email_is_verified'
        )
        ->where('email', $request->email)
        ->where('email_is_verified', true)
        ->first();

        if($emailConfirmed !== null)
            return true;
        else
            return false;
            
    }

    public function checkConfirm(Request $request){

    }


    public function checkIfEmailIsConfirmed($request){

        $emailConfirmed = EmailConfirmation::select(
            'email', 'email_is_verified'
        )
        ->where('email', $request->email)
        ->where('email_is_verified', true)
        ->first();

        if($emailConfirmed !== null)
            return true;
        else
            return false;

    }

    public function sendCode(Request $request): bool {

        $request->validated();

        $user = array("email" => $request->email, "first_name" => $request->first_name);

        $pin = mt_rand(100000, 999999);

        EmailConfirmation::updateOrCreate(
            [
                'email' => $request->email,
                'email_is_verified' => false
            ], [
                'confirmation_token' => $pin
        ]);

        Mail::queue(new EmailConfirmationEmail($user, $propertyInfo, $pin, $lang));

        return true;

    }

    public function validateEmailConfirmCode(ValidateEmailConfirmCode $request): bool{

        $emailToConfirm = EmailConfirmation::select(
            'email', 'email_is_verified'
        )
        ->where('email', $request->email)
        ->where('email_is_verified', false)
        ->where('confirmation_token', $request->confirm_code)
        ->first();

        if($emailToConfirm !== null){
            EmailConfirmation::where('email', $request->email)
            ->where('email_is_verified', false)
            ->where('confirmation_token', $request->confirm_code)
            ->update(
                ['email_is_verified' => true]
            );
        } else {
            return false;
        }

        return true;

    }


    public function checkConfirmCode(CheckEmailConfirmCode $request){

        $now = Carbon::now();

        $emailConfirmed = EmailConfirmation::select(
            'email', 'email_is_verified'
        )
        ->where('email', $request->email)
        ->where('expires_at', '>', $now->toDateTimeString())
        ->where('email_is_verified', true)
        ->first();

        if($emailConfirmed !== null)
            return true;
        else
            return false;

    }

}
