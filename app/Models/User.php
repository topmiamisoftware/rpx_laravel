<?php

namespace App\Models;

use Auth;
use Mail;

use App\Mail\User\AccountCreated;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
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
use Carbon\Carbon;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Cashier;

/**
 * @property mixed $business
 * @property mixed $trial_ends_at
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable, HasFactory, SoftDeletes, Billable;

    protected $casts = [
        'trial_ends_at' => 'date',
    ];

    protected $fillable = ['trial_ends_at'];

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
        return $this->hasOne('App\Models\UserLocation', 'id');
    }

    public function business(){
        return $this->hasOne('App\Models\Business', 'id');
    }

    public function spotbieUser(){
        return $this->hasOne('App\Models\SpotbieUser', 'id');
    }

    public function defaultImages(){
        return $this->hasMany('App\Models\DefaultImages', 'id');
    }

    public function myFavorites(){
        return $this->hasMany('App\Models\MyFavorites', 'user_id');
    }

    public function loyaltyPointBalance(){
        return $this->hasMany('App\Models\LoyaltyPointBalance', 'id');
    }

    public function loyaltyPointBalanceAggregator(){
        return $this->hasOne('App\Models\LoyaltyPointBalanceAggregator', 'id');
    }

    public function loyaltyPointLedger(){
        return $this->hasMany('App\Models\LoyaltyPointLedger', 'user_id');
    }

    public function redeemed(){
        return $this->hasMany('App\Models\RedeemableItems', 'redeemer_id');
    }

    public function signUp(Request $request){
        $validatedData = $request->validate([
            'username' => ['required', 'unique:users', 'max:35', 'min:1', new Username],
            'email' => ['required', 'unique:users', 'email'],
            'password' => ['required', new Password],
            'route' => ['required', 'string']
        ]);

        if($validatedData['route'] == '/business') {
            $accountType = 0;
        } else {
            $accountType = 4;
        }

        $user = new User();

        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->password = Hash::make($validatedData['password']);
        $user->uuid = Str::uuid();

        $newSpotbieUser = new SpotbieUser();

        $newSpotbieUser->first_name = '';
        $newSpotbieUser->last_name = '';
        $newSpotbieUser->user_type = $accountType;

        $description = "Welcome to my Spotbie profile.";

        $newSpotbieUser->description = $description;
        $newSpotbieUser->last_known_ip_address = $request->ip;

        $loyaltyPointBalance = new LoyaltyPointBalance();

        DB::transaction(function () use ($user, $newSpotbieUser, $loyaltyPointBalance){
            $user->createAsStripeCustomer();
            $user->save();

            $newSpotbieUser->id = $user->id;
            $newSpotbieUser->save();

            if( $newSpotbieUser->user_type != '0' ){
                $lpAggregator = new LoyaltyPointBalanceAggregator();
                $lpAggregator->id = $user->id;
                $lpAggregator->balance = 0;
                $lpAggregator->save();
            }
        }, 3);

        $newSpotbieUser = $user->spotbieUser()->select('default_picture', 'user_type')->first();

        //Start the session
        Auth::login($user);

        $this->sendConfirmationEmail();

        $user = Auth::user();
        $user = $user
        ->select('id', 'username', 'email')
        ->where('id', $user->id)
        ->first();

        $signUpResponse = array(
            'token_info' => $this->respondWithToken(Auth::refresh()),
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
            'remember_me_opt' => ['required', 'string'],
            'route' => ['required', 'string']
        ]);

        $login = $validatedData['login'];
        $password = $validatedData['password'];
        $remember_me = $validatedData['remember_me_opt'];
        $route = $validatedData['route'];

        if($route == '/business'){
            //Set account to not set and let the user pick their business account type later on.
            $accountType = 0;
        } else {
            //Set the account type to personal.
            $accountType = 4;
        }

        $searchUser = User::onlyTrashed()
            ->select('id', 'password')
            ->where(function($query) use ($login){
                    $query->where('username', $login)
                        ->orWhere('email', $login);
                })
            ->first();

        if($searchUser !== null && Hash::check($password, $searchUser->password)) {
            $searchUser->restore();
        }

        if (!Auth::attempt(['email' => $login, 'password' => $password]) &&
            !Auth::attempt(['username' => $login, 'password' => $password])
        ) {
            $login_failed = true;
        } else {
            $login_failed = false;
        }

        if($login_failed){
            $loginResponse = array(
                'message' => 'invalid_cred'
            );
        } else {
            $user = User::select('id', 'username', 'stripe_id')->where(function($query) use ($login){
                    $query->where('username', $login)
                            ->orWhere('email', $login);
                })
                ->first();

            $accountTypeCheck = $this->checkAccountType($accountType, $user);

            if($accountTypeCheck !== true) {
                return $accountTypeCheck;
            }

            if($user->stripe_id == null) {
                $user->createAsStripeCustomer();
            }

            $spotbieUser = $user->spotbieUser()->select('default_picture', 'user_type')->first();

            //Start the session
            Auth::login($user, $remember_me);
            $token = Auth::refresh();

            if($remember_me == '1'){
                Auth::user()->remember_token = $token;
            } else {
                Auth::user()->remember_token = null;
            }

            Auth::user()->save();

            $user = Auth::user();

            $loginResponse = array(
                'token_info' => $this->respondWithToken($token),
                'message' => 'success',
                'user' => $user,
                'spotbie_user' => $spotbieUser
            );
        }
        return response($loginResponse);
    }

    public function checkAccountType(int $accountType, User $user)
    {
        if( ($accountType === 0) &&
            ($user->spotbieUser->user_type == 1 ||
            $user->spotbieUser->user_type  == 2 ||
            $user->spotbieUser->user_type  == 3
            )
        ) {
            return true;
        } else if($user->spotbieUser->user_type !== $accountType) {
            return response([
                "message" => "wrong_account_type",
                "account_type" => $accountType,
                "sb_acc_type" => $user->spotbieUser->user_type
            ]);
        } else {
            return true;
        }
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
            $user = Auth::user();

            $userId = $user->id;

            if($user->stripe_id !== null){
                $userBillable = Cashier::findBillable($user->stripe_id);
                $businessMembership = $userBillable->subscribed($user->id);
            } else {
                $businessMembership = null;
            }
        } else {
            $msg = 'not_logged_in';
            $businessMembership = null;
            $userId = null;
        }

        $response = array(
            'message' => $msg,
            'user_id' => $userId,
            'businessMembership' => $businessMembership
        );

        return response($response);
    }

    private function sendConfirmationEmail (){
        $user = Auth::user();
        $spotbieUser = $user->spotbieUser()->first();

        Mail::to($user->email, $user->username)
        ->send(new AccountCreated($user, $spotbieUser) );
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
            ->select('user_type', 'first_name', 'last_name', 'phone_number')
            ->get()[0];

        $business = $user
            ->business()
            ->select(
                'id', 'name', 'description', 'address',
                'city', 'country', 'line1', 'line2', 'postal_code', 'state', 'categories',
                'photo', 'is_verified', 'qr_code_link', 'loc_x', 'loc_y', 'created_at', 'updated_at')
            ->get();

        $isSubscribed = false;
        $isTrial = false;

        $trialEndsAt = $user->trial_ends_at;

        if( count($business) > 0) {
            $business = $business[0];

            $userBillable = Cashier::findBillable($user->stripe_id);

            $isSubscribed = $userBillable->subscribed($user->id);

            if($trialEndsAt !== null && $trialEndsAt->gt( Carbon::now() ) && !$isSubscribed) $isTrial = true;

            if($isSubscribed){
                $trialEndsAt = Carbon::createFromTimestamp($user->subscription($user->id)->asStripeSubscription()->current_period_end);
            }

        } else {
            $business = null;
            $isSubscribed = false;
            $isTrial = false;
            $trialEndsAt = null;
        }

        $settingsResponse = array(
            'success' => true,
            'user' => $userSettings,
            'spotbie_user' => $spotbieUserSettings,
            'business' => $business,
            'is_subscribed' => $isSubscribed,
            'is_trial' => $isTrial,
            'trial_ends_at' => $trialEndsAt
        );

        return response($settingsResponse);
    }

    public function saveSettings(Request $request){
        $user = Auth::user();

        if($user->username === $request->username) {
            $usernameValidators = 'required|string|max:35|min:1';
        } else {
            $usernameValidators = 'required|string|unique:users|max:35|min:1';
        }

        if($user->email === $request->email) {
            $emailValidators = 'required|email';
        } else {
            $emailValidators = 'required|email|unique:users';
        }

        $validatedData = $request->validate([
            'username' => $usernameValidators,
            'email' => $emailValidators,
            'first_name' => ['required', new FirstName],
            'last_name' => ['required', new LastName],
            'account_type' => 'required|numeric',
            'phone_number' => 'string|max:35|nullable'
        ]);

        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->spotbieUser->first_name = $validatedData['first_name'];
        $user->spotbieUser->last_name = $validatedData['last_name'];
        $user->spotbieUser->phone_number = $validatedData['phone_number'];
        $user->spotbieUser->user_type = $validatedData['account_type'];

        DB::transaction(function () use ($user){
            $user->save();
            $user->spotbieUser->save();
        }, 3);

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

        $defaultImages = $this->defaultImages()->select('default_image_url')->get();

        $response = array(
            'message' => 'success',
            'user' => $user,
            'spotbie_user' => $spotbieUser,
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

        if($user !== null){
            $userId = $user->id;

            $status = IlluminatePassword::sendResetLink(
                $request->only('email')
            );

            $isGoogleUser = GoogleUser::find($user->id);
            $isFacebookUser = FacebookUser::find($user->id);

            if($isGoogleUser){
                $status = 'social_account';
            } else if($isFacebookUser){
                $status = 'social_account';
            }
        } else {
            $status = 'invalid_email';
        }

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
            'password' => ['nullable', new Password],
            'is_social_account' => ['required', 'boolean']
        ]);

        $passwordCheck = false;

        if($validatedData['is_social_account'] === true){
            $passwordCheck = true;
        } else {

            if( Hash::check($validatedData['password'], $user->password) )
                $passwordCheck = true;
            else
                $success = false;

        }

        if($passwordCheck){

            //Deactivate all Stripe Memberships
            $deleteStripeMembership = $this->cancelMembership();

            if($deleteStripeMembership){
                if( $user->delete() ) $success = true;
            } else
                $success = false;

        }

        $response = array(
            'success' => $success
        );

        return response($response);
    }

    public function activate(){}

    public function uniqueEmail(Request $request){
        $emailConfirmed = EmailConfirmation::select(
            'email', 'email_is_verified'
        )
        ->where('email', $request->email)
        ->where('email_is_verified', true)
        ->first();

        if($emailConfirmed !== null) {
            return true;
        } else {
            return false;
        }
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

        if($emailConfirmed !== null) {
            return true;
        } else {
            return false;
        }
    }

    public function sendCode(Request $request): bool {
        $request->validated();

        $user = array("email" => $request->email, "first_name" => $request->first_name);

        $pin = mt_rand(100000, 999999);

        EmailConfirmation::updateOrCreate([
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

    public function businessMembership(Request $request){
        $validatedData = $request->validate([
            "uuid" => ['required', 'string', 'max:36'],
            "payment_method" => [
                "id" => ['required', 'string']
            ]
        ]);

        $uuid = $validatedData['uuid'];
        $paymentMethodId = $validatedData['payment_method']['id'];

        $price_name = config('spotbie.business_subscription_price');

        $user = User::where('uuid', $uuid)->get();

        if($user->first()){
            $userStripeId = $user[0]->stripe_id;

            $user = Cashier::findBillable($userStripeId);

            $user->updateDefaultPaymentMethod($paymentMethodId);

            //Create the subscription with the payment method provided by the user.
            $user->newSubscription($user->id, [$price_name] )->create($paymentMethodId);

            //Set existing trial_ends_at to now
            $user->trial_ends_at = Carbon::now();
            $user->save();
        }

        $user = $user->refresh();

        $response = array(
            'success' => true,
            'user' => $user
        );

        return response($response);
    }

    public function membershipStatus(Request $request)
    {
        $validatedData = $request->validate([
            "uuid" => ['required', 'string', 'max:36']
        ]);

        $user = User::where('uuid', $validatedData['uuid'])->get();
        $membershipInfo = null;

        if( $user->first() ) {
            $membershipInfo = Cashier::findBillable($user[0]->stripe_id);

            if($membershipInfo !== null) {
                $membershipInfo = $membershipInfo->subscribed($user[0]->id);
            }
        }

        $response = array(
            'success' => true,
            'membershipInfo' => $membershipInfo
        );

        return response($response);
    }

    public function cancelMembership(){
        $user = Auth::user();

        $userBillable = Cashier::findBillable($user->stripe_id);

        if( !!is_null($userBillable) ){
            if( $userBillable->subscribed($user->id) ){
                $userBillable->subscription($user->id)->cancelNow();
            }
        }

        //We also need to cancel all of the user's ads if they have any.
        $userAdList = Ads::withTrashed()
        ->where('business_id', '=', $user->id)
        ->get();

        if( $userAdList->first() ) {
            foreach ( $userAdList as $userAd ) {
                if( $userBillable->subscribed($userAd->id) ) {
                    $userBillable->subscription($userAd->id)->cancelNow();
                }
                $userAd->delete();
            }
        }

        $response = array(
            'success' => true
        );

        return response($response);
    }
}
