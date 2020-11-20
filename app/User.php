<?php

namespace App;

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
use Twilio\Rest\Client;

use App\Rules\FirstName;
use App\Rules\LastName;
use App\Rules\PhoneNumber;
use App\Rules\Password;
use App\Rules\Username;


class User extends Authenticatable
{
    
    use Notifiable, HasFactory, SoftDeletes;  

    public function userLocation(){
        return $this->hasOne('App\UserLocation');
    }

    public function streamPosts(){
        return $this->hasMany('App\StreamPost');
    }

    public function webOptions(){
        return $this->hasOne('App\WebOptions');
    }

    public function contactMe(){
        return $this->hasOne('App\ContactMe');
    }

    public function spotbieUser(){        
        return $this->hasOne('App\SpotbieUser', 'id');
    }

    public function relationships(){                
        return $this->hasMany('App\Friendship');
    }    

    public function albums(){
        return $this->hasMany('App\Album');
    }

    public function defaultImages(){
        return $this->hasMany('App\DefaultImages');
    }

    public function myFavorites(){
        return $this->hasMany('App\MyFavorites', 'user_id');
    }

    public function signUp(Request $request){

        $validatedData = $request->validate([
            'username' => ['required', 'unique:users', 'max:35', 'min:1', new Username],
            'first_name' => ['required', new FirstName],
            'last_name' => ['required', new LastName],
            'email' => ['required', 'unique:users', 'email'],
            'phone_number' => ['required', 'unique:spotbie_users', new PhoneNumber],
            'password' => ['required', new Password, 'confirmed']
        ]);
        
        $user = new User();
        
        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($validatedData['password']);
        
        $user->confirm = strtoupper(Str::random(6));
        
        $newSpotbieUser = new SpotbieUser();
        
        $newSpotbieUser->default_picture = 'defaults/user.png';
        $newSpotbieUser->first_name = $validatedData['first_name'];
        $newSpotbieUser->last_name = $validatedData['last_name'];

        $fullName = $validatedData['first_name'] . ' ' . $validatedData['last_name'];
        $description = 'Hello my name is ' . $fullName . '. Welcome to my Spotbie profile.'; 

        $newSpotbieUser->description = $description;
        $newSpotbieUser->last_known_ip_address = $request->ip;
        $newSpotbieUser->phone_number = $validatedData['phone_number'];
        $newSpotbieUser->phone_is_confirmed = false;

        DB::transaction(function () use ($user, $newSpotbieUser){
            $user->save();
            $newSpotbieUser->id = $user->id;
            $newSpotbieUser->save();
        });

        //Start the session
        Auth::login($user);

        $this->sendSignUpConfirmationSms();
        $this->sendConfirmationEmail();
        
        $user = Auth::user();
        $user = $user->select('id', 'username', 'email')->first();

        $signUpResponse = array(
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
        

        if(!Auth::attempt(['email' => $login, 'password' => $password], $remember_me)
        && !Auth::attempt(['username' => $login, 'password' => $password], $remember_me)
        && !Auth::viaRemember())
            $login_failed = true;
        else
            $login_failed = false;

        //TODO: Login Credentials do not exist. Send invalid login attempt to front end.
        if($login_failed){

            $loginResponse = array(
                'message' => 'invalid_cred'
            );      

        } else {

            if(Auth::viaRemember()){
                $user = Auth::user();
            } else {
                $user = User::select('id', 'username')->where(
                    function($query) use ($login){
                        $query->where('username', $login)
                            ->orWhere('email', $login);
                    }
                )
                ->first();
            }

            $spotbieUser = $user->spotbieUser()->select('default_picture')->first();

            if($user->confirm == '1')
                $message = 'success';
            else
                $message = 'confirm';

            $loginResponse = array(
                'message' => $message,
                'user' => $user,
                'spotbie_user' => $spotbieUser
            );    

            //Start the session
            Auth::login($user, $remember_me);

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

    private function sendSignUpConfirmationSms(){

        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");

        $client = new Client($account_sid, $auth_token);

        $user = Auth::user();

        $account_code = $user->confirm;
        $recipient = $user->spotbieUser->phone_number;

        $message = "Hello " . $user->username . ". Welcome to SpotBie. Verify your account through this link: https://spotbie.com?c=".$account_code.", through the e-mail we've sent you, or enter this code next time you log-in: ".$account_code;

        $client->messages->create($recipient, 
                ['from' => $twilio_number, 'body' => $message] );
        
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
            "username" => $user->username,
            "email" => $user->email
        );

        $spotbieUserSettings = $user
        ->spotbieUser()
        ->select('first_name', 'last_name', 'animal', 'ghost_mode', 'privacy', 'phone_number')
        ->get()[0];

        $settingsResponse = array(
            'message' => 'success',
            'user' => $userSettings,
            'spotbie_user' => $spotbieUserSettings
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
                'phone_number' => ['required', new PhoneNumber],
                'ghost_mode' => 'required|boolean',
                'privacy' => 'required|boolean',
                'animal' => 'required|string'
            ]);

        } else {

            $validatedData = $request->validate([
                'username' => 'required|string|unique:users|max:35|min:1',
                'email' => 'required|email',
                'first_name' => ['required', new FirstName],
                'last_name' => ['required', new LastName],
                'phone_number' => ['required', new PhoneNumber],
                'ghost_mode' => 'required|boolean',
                'privacy' => 'required|boolean',
                'animal' => 'required|string'
            ]);

        }

        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];

        $user->save();

        $user->spotbieUser->first_name = $validatedData['first_name'];
        $user->spotbieUser->last_name = $validatedData['last_name'];
        $user->spotbieUser->phone_number = $validatedData['phone_number'];
        $user->spotbieUser->ghost_mode = $validatedData['ghost_mode'];
        $user->spotbieUser->privacy = $validatedData['privacy'];
        $user->spotbieUser->animal = $validatedData['animal'];
        
        $user->spotbieUser->save();

        $response = array(
            'success' => true
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
            'email_or_phone' => 'required|string'
        ]);
        
        $user = User::select('id', 'email')
        ->where('email', $validatedData['email_or_phone'])
        ->first();

        if($user === null){

            $user = SpotbieUser::select('id')
            ->where('phone_number', $validatedData['email_or_phone'])
            ->first();

            $user = User::select('id', 'email')
            ->where('id', $user->id)
            ->first();
            
        }

        $spotbieUser = SpotbieUser::select('id', 'phone_number')
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

}
