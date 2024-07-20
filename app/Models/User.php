<?php

namespace App\Models;

use App\Jobs\SendSystemSms;
use App\Jobs\SendAccountCreatedThroughBusinessSms;
use Auth;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Log;
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

    protected $hidden = ['password', 'stripe_id', 'pm_last_four', 'pm_type', 'created_at', 'delete_at', 'end_of_month'];

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
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => Auth::factory()->getTTL() * 60,
        ]);
    }

    public function userLocation()
    {
        return $this->hasOne('App\Models\UserLocation', 'id');
    }

    public function business()
    {
        return $this->hasOne('App\Models\Business', 'id');
    }

    public function spotbieUser()
    {
        return $this->hasOne('App\Models\SpotbieUser', 'id');
    }

    public function defaultImages()
    {
        return $this->hasMany('App\Models\DefaultImages', 'id');
    }

    public function myFavorites()
    {
        return $this->hasMany('App\Models\MyFavorites', 'user_id');
    }

    public function loyaltyPointBalance()
    {
        return $this->hasMany('App\Models\LoyaltyPointBalance', 'user_id');
    }

    public function loyaltyPointBalanceAggregator()
    {
        return $this->hasOne('App\Models\LoyaltyPointBalanceAggregator', 'id');
    }

    public function loyaltyPointLedger()
    {
        return $this->hasMany('App\Models\LoyaltyPointLedger', 'user_id');
    }

    public function redeemed()
    {
        return $this->hasMany('App\Models\RedeemableItems', 'redeemer_id');
    }

    public function signUp(Request $request)
    {
        $validatedData = $request->validate([
            'username' => ['required', 'unique:users', 'max:35', 'min:1', new Username],
            'email'    => ['required', 'unique:users', 'email'],
            'password' => ['required', new Password],
            'route'    => ['required', 'string'],
        ]);

        if ($validatedData['route'] == '/business')
        {
            $accountType = 0;
        }
        else
        {
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

        $description = 'Welcome to my Spotbie profile.';

        $newSpotbieUser->description = $description;
        $newSpotbieUser->last_known_ip_address = $request->ip;

        DB::transaction(function () use ($user, $newSpotbieUser) {
            $user->createAsStripeCustomer();
            $user->save();

            $newSpotbieUser->id = $user->id;
            $newSpotbieUser->save();

            // Only make the aggregated Balance if the user is not a business
            if ($newSpotbieUser->user_type != '0')
            {
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

        $signUpResponse = [
            'token_info'   => $this->respondWithToken(Auth::refresh()),
            'message'      => 'success',
            'user'         => $user,
            'spotbie_user' => $newSpotbieUser,
        ];

        return response($signUpResponse);
    }

    public function logIn(Request $request)
    {
        $validatedData = $request->validate([
            'login'           => ['required', 'string'],
            'password'        => ['required', new Password],
            'timezone'        => ['required', 'string'],
            'remember_me_opt' => ['required', 'string'],
            'route'           => ['required', 'string'],
        ]);

        $login = $validatedData['login'];
        $password = $validatedData['password'];
        $remember_me = $validatedData['remember_me_opt'];
        $route = $validatedData['route'];

        if ($route == '/business')
        {
            //Set account to not set and let the user pick their business account type later on.
            $accountType = 0;
        }
        else
        {
            //Set the account type to personal.
            $accountType = 4;
        }

        $searchUser = User::onlyTrashed()
            ->select('id', 'password')
            ->where(function ($query) use ($login) {
                $query->where('username', $login)
                    ->orWhere('email', $login);
            })
            ->first();

        if ($searchUser !== null && Hash::check($password, $searchUser->password))
        {
            $searchUser->restore();
        }

        if (!Auth::attempt(['email' => $login, 'password' => $password]) &&
            !Auth::attempt(['username' => $login, 'password' => $password])
        ) {
            $login_failed = true;
        }
        else
        {
            $login_failed = false;
        }

        if ($login_failed)
        {
            $loginResponse = [
                'message' => 'invalid_cred',
                'user' => $searchUser
            ];
        }
        else
        {
            $user = User::select('id', 'username', 'stripe_id')->where(function ($query) use ($login) {
                $query->where('username', $login)
                        ->orWhere('email', $login);
            })->first();

            $accountTypeCheck = $this->checkAccountType($accountType, $user);

            if ($accountTypeCheck !== true)
            {
                return $accountTypeCheck;
            }

            if ($user->stripe_id == null)
            {
                $user->createAsStripeCustomer();
            }

            $spotbieUser = $user->spotbieUser()->select('default_picture', 'user_type')->first();

            // Start the session
            Auth::login($user, $remember_me);
            $token = Auth::refresh();

            if ($remember_me == '1')
            {
                Auth::user()->remember_token = $token;
            }
            else
            {
                Auth::user()->remember_token = null;
            }

            Auth::user()->save();

            $user = Auth::user();

            $loginResponse = [
                'token_info'   => $this->respondWithToken($token),
                'message'      => 'success',
                'user'         => $user,
                'spotbie_user' => $spotbieUser,
            ];
        }
        return response($loginResponse);
    }

    public function checkAccountType(int $accountType, User $user)
    {
        if ($accountType === 0 &&
            ($user->spotbieUser->user_type == 1 ||
             $user->spotbieUser->user_type == 2 ||
             $user->spotbieUser->user_type == 3)
        ) {
            return true;
        }
        elseif ($user->spotbieUser->user_type !== $accountType)
        {
            return response([
                'message'      => 'wrong_account_type',
                'account_type' => $accountType,
                'sb_acc_type'  => $user->spotbieUser->user_type,
            ]);
        }
        else
        {
            return true;
        }
    }

    public function logOut(Request $request)
    {
        Auth::logout();

        $logoutResponse = [
            'success' => true,
        ];

        return response($logoutResponse);
    }

    public function closeBrowser(Request $request)
    {
        if (Auth::user()->remember_me !== null)
        {
            Auth::logout();
        }

        $logoutResponse = [
            'success' => true,
        ];

        return response($logoutResponse);
    }

    public function checkIfLoggedIn()
    {
        if (Auth::check())
        {
            $msg = '1';
            $user = Auth::user();

            $userId = $user->id;

            if ($user->stripe_id !== null)
            {
                $userBillable = Cashier::findBillable($user->stripe_id);
                $businessMembership = $userBillable->subscribed($user->id);
            }
            else
            {
                $businessMembership = null;
            }
        }
        else
        {
            $msg = 'not_logged_in';
            $businessMembership = null;
            $userId = null;
        }

        $response = [
            'message'            => $msg,
            'user_id'            => $userId,
            'businessMembership' => $businessMembership,
        ];

        return response($response);
    }

    private function sendConfirmationEmail(User $user = null, SpotbieUser $spotbieUser = null, bool $withLink = false)
    {
        if (is_null($user)) {
            $user = Auth::user();
            $spotbieUser = $user->spotbieUser()->first();
        }

        Log::info('HI' . $user->email);

        $credentials = array(
            "email" => $user->email
        );

        if ($withLink) {
           IlluminatePassword::sendResetLink($credentials);
        }

        Mail::to($user->email, $user->username)
            ->send(new AccountCreated($user, $spotbieUser, $withLink));
    }

    private function sendConfirmationSms($user = null, $spotbieUser = null, $businessName = null) {
        $sms = app(SystemSms::class)->createSettingsSms($user, $spotbieUser->phone_number);

        SendAccountCreatedThroughBusinessSms::dispatch($user, $sms, $spotbieUser->phone_number, $businessName)
            ->onQueue('sms.miami.fl.1');
    }

    public function getSettings()
    {
        $user = Auth::user();

        $userSettings = [
            'hash'     => $user->uuid,
            'username' => $user->username,
            'email'    => $user->email,
        ];

        $spotbieUserSettings = $user
            ->spotbieUser()
            ->select('user_type', 'first_name', 'last_name', 'phone_number')
            ->get()[0];

        $business = $user
            ->business()
            ->select(
                'id',
                'name',
                'description',
                'address',
                'city',
                'country',
                'line1',
                'line2',
                'postal_code',
                'state',
                'categories',
                'photo',
                'is_verified',
                'qr_code_link',
                'loc_x',
                'loc_y',
                'created_at',
                'updated_at',
                'is_food_truck'
            )->get();

        $nextPayment = null;
        $endsAt = null;

        if (count($business) > 0)
        {
            $business = $business[0];

            $userBillable = Cashier::findBillable($user->stripe_id);
            if (count($userBillable->subscriptions) > 0) {
                $userSubscriptionPlan = $userBillable->subscriptions[0]->stripe_price;
            } else {
                $userSubscriptionPlan = null;
            }

            switch($userSubscriptionPlan)
            {
                case config('spotbie.business_subscription_price_1_2'):
                    $userSubscriptionPlan = 'spotbie.business_subscription_price_1_2';
                    break;
                case config('spotbie.business_subscription_price_2_2'):
                    $userSubscriptionPlan = 'spotbie.business_subscription_price_2_2';
                    break;
                case config('spotbie.business_subscription_price1'):
                    $userSubscriptionPlan = 'spotbie.business_subscription_price1';
                    break;
                case config('spotbie.business_subscription_price'):
                    $userSubscriptionPlan = 'spotbie.business_subscription_price';
                    break;
                default:
                    $userSubscriptionPlan = null;
            }

            $isSubscribed = $userBillable->subscribed($user->id);

            if ($isSubscribed)
            {
                $nextPayment = Carbon::createFromTimestamp($user->subscription($user->id)->asStripeSubscription()->current_period_end);
                if($user->subscription($user->id)->asStripeSubscription()->cancel_at) {
                    $endsAt = Carbon::createFromTimestamp($user->subscription($user->id)->asStripeSubscription()->cancel_at);
                }
            }

            $loyaltyPointBalance = $user->business->loyaltyPointBalance()->first();
        }
        else
        {
            $business = null;
            $isSubscribed = false;
            $userSubscriptionPlan = null;
            $loyaltyPointBalance = null;
        }

        $settingsResponse = [
            'success'               => true,
            'user'                  => $userSettings,
            'spotbie_user'          => $spotbieUserSettings,
            'business'              => $business,
            'is_subscribed'         => $isSubscribed,
            'userSubscriptionPlan'  => $userSubscriptionPlan,
            'loyalty_point_balance' => $loyaltyPointBalance,
            'next_payment'          => $nextPayment,
            'ends_at' => $endsAt,
        ];

        return response($settingsResponse);
    }

    public function saveSettings(Request $request)
    {
        $user = Auth::user();

        if ($user->username === $request->username)
        {
            $usernameValidators = 'required|string|max:35|min:1';
        }
        else
        {
            $usernameValidators = 'required|string|unique:users|max:35|min:1';
        }

        if ($user->email === $request->email)
        {
            $emailValidators = 'required|email';
        }
        else
        {
            $emailValidators = 'required|email|unique:users';
        }

        $validatedData = $request->validate([
            'username'     => $usernameValidators,
            'email'        => $emailValidators,
            'first_name'   => ['required', new FirstName],
            'last_name'    => ['required', new LastName],
            'account_type' => 'required|numeric',
            'phone_number' => 'string|unique:spotbie_users|max:35|nullable',
        ]);

        $user->username = $validatedData['username'];
        $user->email = $validatedData['email'];
        $user->spotbieUser->first_name = $validatedData['first_name'];
        $user->spotbieUser->last_name = $validatedData['last_name'];
        $user->spotbieUser->user_type = $validatedData['account_type'];

        $s = SpotbieUser::where('phone_number', '+1'.$validatedData['phone_number'])
            ->where('phone_number', $validatedData['phone_number'])
            ->count();

        if ($s > 0) {
            return new Exception('The phone number is already in use.', 422);
        }

        DB::transaction(function () use ($user, $validatedData) {
            if (! array_key_exists('phone_number', $validatedData)) {
                $user->spotbieUser->phone_number = null;
            }

            $user->save();
            $user->spotbieUser->save();

            if (array_key_exists('phone_number', $validatedData) && $user->spotbieUser->sms_opt_in === 0) {
                $sms = app(SystemSms::class)->createSettingsSms($user, $validatedData['phone_number']);
                SendSystemSms::dispatch($user, $sms, $validatedData['phone_number'])
                    ->onQueue('sms.miami.fl.1');
            } else {
                // User already opted-in, no need to send opt-in confirmation message.
                if(array_key_exists('phone_number', $validatedData)){
                    $user->spotbieUser->phone_number = $validatedData['phone_number'];
                } else {
                    $user->spotbieUser->phone_number = null;
                }
                $user->spotbieUser->save();

                Log::info(
                    '[UserService]-[sendSettingsSms]: Phone Number Updated' .
                    ', User ID: ' . $user->id .
                    ', Phone-Number: ' . $user->spotbieUser->phone_number
                );
            }

        }, 3);

        $response = [
            'success' => true,
            'user'    => $user,
        ];

        return response($response);
    }

    public function confirmAccount(Request $request)
    {
    }

    public function savePassword(Request $request)
    {
        $validatedData = $request->validate([
            'password' => ['required', new Password, 'confirmed'],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        $response = [
            'message' => 'success',
        ];

        return $response;
    }

    public function getUser(Request $request)
    {
        $business = Auth::user();

        $validatedData = $request->validate([
            'phone_number' => 'string|max:35|required',
        ]);

        $spotbieUser = SpotbieUser::
            select('id', 'first_name', 'last_name', 'phone_number')
            ->where('phone_number', $validatedData['phone_number'])
            ->first();

        if (! is_null($spotbieUser)) {
            $user = $this->find($spotbieUser->id)->only('email', 'username');

            $lpBalanceInBusiness = LoyaltyPointBalance::select('balance', 'balance_aggregate')
                ->where('from_business', $business->id)
                ->where('user_id', $spotbieUser->id)
                ->first();

            $lpBalance = LoyaltyPointBalanceAggregator::where('id', $spotbieUser->id)->first();
            $message = "success";
        } else {
            $user = null;
            $lpBalanceInBusiness = null;
            $lpBalance = null;
            $message = "User not found.";
        }

        $response = [
            'message' => $message,
            'user' => $user,
            'spotbie_user' => $spotbieUser,
            'lp_balance' => $lpBalance,
            'lp_in_business' => $lpBalanceInBusiness,
        ];

        return response($response);
    }

    public function privateProfile()
    {
        $spotbieUser = $this->spotbieUser()->select('first_name', 'last_name', 'description', 'default_picture')->first();
        $defaultImages = $this->defaultImages()->select('default_image_url')->get();
        $business = $this->business;

        $response = [
            'user'           => $this->only('id', 'username', 'email'),
            'spotbie_user'   => $spotbieUser,
            'default_images' => $defaultImages,
            'business'       => $business,
        ];

        return response($response);
    }

    public function setPassResetPin(Request $request)
    {
        $success = false;

        $validatedData = $request->validate([
            'email' => 'required|string',
        ]);

        $user = User::select('id', 'email')
        ->where('email', $validatedData['email'])
        ->first();

        if ($user !== null)
        {
            $userId = $user->id;

            $status = IlluminatePassword::sendResetLink(
                $request->only('email')
            );
        }
        else
        {
            $status = 'invalid_email';
        }

        $success = true;

        $response = [
            'success' => $success,
            'user'    => $user,
            'status'  => $status,
        ];

        return response($response);
    }

    public function completePassReset(Request $request)
    {
        $success = true;

        $validatedData = $request->validate([
            'email'    => ['required', 'email'],
            'token'    => ['required', 'string'],
            'password' => ['required', new Password, 'confirmed'],
        ]);

        $status = IlluminatePassword::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                $user->setRememberToken(Str::random(60));

                event(new IlluminatePassword($user));
            }
        );

        $response = [
            'success' => $success,
            'status'  => $status,
        ];

        return response($response);
    }

    public function changePassword(Request $request)
    {
        $success = false;

        $user = Auth::user();

        $validatedData = $request->validate([
            'password'         => ['required', new Password, 'confirmed'],
            'current_password' => ['required', new Password],
        ]);

        if (Hash::check($validatedData['current_password'], $user->password))
        {
            $user->password = Hash::make($validatedData['password']);
            $user->save();
            $success = true;
            $message = 'saved';
        }
        else
        {
            $message = 'SB-E-000';
        }

        $response = [
            'success' => $success,
            'message' => $message,
        ];

        return response($response);
    }

    public function deactivate(Request $request)
    {
        $success = false;

        $user = Auth::user();

        $validatedData = $request->validate([
            'password'          => ['nullable', new Password],
            'is_social_account' => ['required', 'boolean'],
        ]);

        $passwordCheck = false;

        if ($validatedData['is_social_account'] === true)
        {
            $passwordCheck = true;
        }
        else
        {
            if (Hash::check($validatedData['password'], $user->password))
            {
                $passwordCheck = true;
            }
            else
            {
                $success = false;
            }
        }

        if ($passwordCheck)
        {
            //Deactivate all Stripe Memberships
            $deleteStripeMembership = $this->cancelMembership();

            if ($deleteStripeMembership)
            {
                if ($user->delete())
                {
                    $success = true;
                }
            }
            else
            {
                $success = false;
            }
        }

        $response = [
            'success' => $success,
        ];

        return response($response);
    }

    public function activate()
    {
    }

    public function uniqueEmail(Request $request)
    {
        $emailConfirmed = EmailConfirmation::select(
            'email',
            'email_is_verified'
        )
        ->where('email', $request->email)
        ->where('email_is_verified', true)
        ->first();

        if ($emailConfirmed !== null)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function checkConfirm(Request $request)
    {
    }

    public function checkIfEmailIsConfirmed($request)
    {
        $emailConfirmed = EmailConfirmation::select(
            'email',
            'email_is_verified'
        )
        ->where('email', $request->email)
        ->where('email_is_verified', true)
        ->first();

        if ($emailConfirmed !== null)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function sendCode(Request $request): bool
    {
        $request->validated();

        $user = ['email' => $request->email, 'first_name' => $request->first_name];

        $pin = mt_rand(100000, 999999);

        EmailConfirmation::updateOrCreate([
            'email'             => $request->email,
            'email_is_verified' => false,
        ], [
            'confirmation_token' => $pin,
        ]);

        Mail::queue(new EmailConfirmationEmail($user, $propertyInfo, $pin, $lang));

        return true;
    }

    public function validateEmailConfirmCode(ValidateEmailConfirmCode $request): bool
    {
        $emailToConfirm = EmailConfirmation::select(
            'email',
            'email_is_verified'
        )
        ->where('email', $request->email)
        ->where('email_is_verified', false)
        ->where('confirmation_token', $request->confirm_code)
        ->first();

        if ($emailToConfirm !== null)
        {
            EmailConfirmation::where('email', $request->email)
            ->where('email_is_verified', false)
            ->where('confirmation_token', $request->confirm_code)
            ->update(
                ['email_is_verified' => true]
            );
        }
        else
        {
            return false;
        }

        return true;
    }

    public function checkConfirmCode(CheckEmailConfirmCode $request)
    {
        $now = Carbon::now();

        $emailConfirmed = EmailConfirmation::select(
            'email',
            'email_is_verified'
        )
        ->where('email', $request->email)
        ->where('expires_at', '>', $now->toDateTimeString())
        ->where('email_is_verified', true)
        ->first();

        if ($emailConfirmed !== null)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function businessMembership(Request $request)
    {
        $validatedData = $request->validate([
            'uuid'           => ['required', 'string', 'max:36'],
            'payment_method' => [
                'id' => ['required', 'string'],
            ],
            'payment_type' => ['required', 'string'],
        ]);

        $uuid = $validatedData['uuid'];
        $paymentMethodId = $validatedData['payment_method']['id'];

        $totalLp = 0;
        $rate = 2;
        switch($validatedData['payment_type'])
        {
            case 'business-membership':
                $priceKey = 'spotbie.business_subscription_price1';
                $totalLp = 2000;
                break;
            case 'business-membership-1':
                $priceKey = 'spotbie.business_subscription_price_1_2';
                $totalLp = 3500;
                break;
            case 'business-membership-2':
                $priceKey = 'spotbie.business_subscription_price_2_2';
                $totalLp = 4750;
                break;
        }

        $price_name = config($priceKey);

        $user = User::where('uuid', $uuid)->get();

        if ($user->first())
        {
            $userStripeId = $user[0]->stripe_id;

            $user = Cashier::findBillable($userStripeId);

            $user->updateDefaultPaymentMethod($paymentMethodId);

            // Create the subscription with the payment method provided by the user.
            $user->newSubscription($user->id, [$price_name])->create($paymentMethodId);

            // Set existing trial_ends_at to now
            $user->trial_ends_at = Carbon::now();

            $loyaltyPointBalance = $user->business->loyaltyPointBalance;
            $loyaltyPointBalance->balance = $totalLp;
            $loyaltyPointBalance->reset_balance = $totalLp;
            $loyaltyPointBalance->loyalty_point_dollar_percent_value = $rate;

            DB::transaction(function () use ($user, $loyaltyPointBalance) {
                $user->save();
                $loyaltyPointBalance->save();
            }, 3);
        }

        $user = $user->refresh();

        $response = [
            'success' => true,
            'user'    => $user,
        ];

        return response($response);
    }

    public function membershipStatus(Request $request)
    {
        $validatedData = $request->validate([
            'uuid'        => ['required', 'string', 'max:36'],
            'paymentType' => ['required', 'string', 'max:56'],
        ]);

        $user = User::where('uuid', $validatedData['uuid'])->first();
        $membershipInfo = null;

        if ($user->first())
        {
            $membershipInfo = Cashier::findBillable($user->stripe_id);

            if ($membershipInfo !== null)
            {
                $membershipInfo = $membershipInfo->subscribed($user->id);
            }
        }

        $response = [
            'success'        => true,
            'membershipInfo' => $membershipInfo,
        ];

        return response($response);
    }

    public function cancelMembership()
    {
        $user = Auth::user();

        $userBillable = Cashier::findBillable($user->stripe_id);

        if (!is_null($userBillable))
        {
            if ($userBillable->subscribed($user->id))
            {
                $userBillable->subscription($user->id)->cancel();
            }
        }

        //We also need to cancel all of the user's ads if they have any.
        $userAdList = Ads::withTrashed()
        ->where('business_id', '=', $user->id)
        ->get();

        if ($userAdList->first())
        {
            foreach ($userAdList as $userAd)
            {
                if ($userBillable->subscribed($userAd->id))
                {
                    $userBillable->subscription($userAd->id)->cancel();
                }
                $userAd->delete();
            }
        }

        $response = [
            'success' => true,
        ];

        return response($response);
    }

    public function createUser(Request $request) {
        $validatedData = $request->validate([
            'email' => ['required', 'unique:users', 'email'],
            'phone_number' => 'string|unique:spotbie_users|max:35|nullable',
            'firstName' => ['required', new FirstName],
        ]);

        $user = new User();
        $user->username = Str::uuid();
        $user->email = $validatedData['email'];
        $user->password = Hash::make('');
        $user->uuid = Str::uuid();

        $newSpotbieUser = new SpotbieUser();
        $newSpotbieUser->first_name = $validatedData['firstName'];
        $newSpotbieUser->last_name = '';
        $newSpotbieUser->user_type = 4;
        $newSpotbieUser->phone_number = $validatedData['phone_number'];

        $message = "success";
        $e = null;

        try {
            $user->save();

            $user = User::where('email', $user->email)->first();

            $newSpotbieUser->id = $user->id;
            $newSpotbieUser->save();

            $newSpotbieUser = SpotbieUser::where('id', $user->id)->first();

            $loggedInUser = Auth::user();
            $businessName = $loggedInUser->business->name;

            DB::transaction(function() use ($user, $newSpotbieUser, $businessName) {
                $lpAggregator = new LoyaltyPointBalanceAggregator();
                $lpAggregator->id = $user->id;
                $lpAggregator->balance = 0;
                $lpAggregator->save();

                $this->sendConfirmationEmail($user, $newSpotbieUser, true);
                $this->sendConfirmationSms($user, $newSpotbieUser, $businessName);
            });

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $message = 'Could not create user account.';
        }

        $signUpResponse = [
            'message'      => $message,
            'user'         => $user,
            'spotbie_user' => $newSpotbieUser,
            'error' => $e
        ];

        return response($signUpResponse);
    }
}
