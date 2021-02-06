<?php

namespace App\Services\User;

use Illuminate\Support\Facades\Log;
use Mail;
use Swift_Mailer;

use Carbon\Carbon;

use App\Helpers\PhoneConfirmation\PhoneConfirmationTwimlHelper;

use App\Http\Requests\v2\User\ValidateUserEmail;
use App\Http\Requests\v2\User\SendSmsConfirmCode;
use App\Http\Requests\v2\User\ValidateSmsConfirmCode;
use App\Http\Requests\v2\User\SendConfirmationEmail;
use App\Http\Requests\v2\User\ValidateEmailConfirmCode;
use App\Http\Requests\v2\User\CheckEmailConfirmCode;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\PhoneConfirmation;
use App\Models\EmailConfirmation;
use App\Mail\User\EmailConfirmation as EmailConfirmationEmail;

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

use GuzzleHttp\Client as GuzzleClient;

use Illuminate\Http\Request;
use Illuminate\Mail\Transport\MailgunTransport;


class UserValidationService
{

    public function __construct(){}

    public function checkIfPhoneIsConfirmed(Request $request): bool{

        $phoneConfirmed = PhoneConfirmation::select(
            'phone_number', 'phone_is_verified'
        )
        ->where('phone_number', $request->phone_number)
        ->where('phone_is_verified', true)
        ->first();

        if($phoneConfirmed !== null)
            return true;
        else
            return false;

    }

    public function makeConfirmationCall(Request $request) {

        $system = $request->system;
        $lang = $request->lang;

        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');

        $confirmationCode = mt_rand(100000, 999999);

        $countryCode = '+1';
        $userPhoneNumber = $countryCode . $request->phone_number;

        PhoneConfirmation::updateOrCreate(
        [
            'phone_number' => $request->phone_number,
            'phone_is_verified' => false
        ], [
            'confirmation_code' => $confirmationCode
        ]);
        
        try {

            $client = new Client($sid, $token);

            $voice = new PhoneConfirmationTwimlHelper($lang);

            $call = $client->account->calls->create(
                $userPhoneNumber,
                config('services.twilio.from'),
                [
                    "record" => true,
                    "twiml" => $voice->getConfirmationCallSpeech($confirmationCode)
                ]
            );
            
            return 'confirmation.sent';

        } catch(TwilioException $e){

            switch($e->getCode()){

                case '21211':
                    return 'phoneNumber.invalid';
                case '21612':
                case '21408':  
                case '21610': 
                case '21614':                
                    return 'phoneNumber.unavailable';
                default: 
                    return 'phoneNumber.invalid';
    
            }

        }

    }

    public function sendSmsConfirmCode(Request $request): string{

        $system = $request->system;
        $lang = $request->lang;

        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');

        $confirmationCode = random_int(100000, 999999);

        $countryCode = '+1';
        $userPhoneNumber = $countryCode . $request->phone_number;

        PhoneConfirmation::updateOrCreate(
        [
            'phone_number' => $request->phone_number,
            'phone_is_verified' => false
        ], [
            'confirmation_code' => $confirmationCode
        ]);

        
        try {

            $client = new Client($sid, $token);

            $langHelper = new PhoneConfirmationTwimlHelper($lang);
    
            $smsText = $langHelper->getConfirmPhoneSmsText($confirmationCode);
    
            $msg = $client->messages->create(
                $userPhoneNumber,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $smsText
                ]
            );

            return 'confirmation.sent';

        } catch(TwilioException $e){

            switch($e->getCode()){

                case '21211':
                    return 'phoneNumber.invalid';
                case '21612':
                case '21408':  
                case '21610': 
                case '21614':                
                    return 'phoneNumber.unavailable';
                default: 
                    return 'phoneNumber.invalid';
    
            }

        }

    }

    public function validateSmsConfirmCode(Request $request): bool{

        $phoneToConfirm = PhoneConfirmation::select(
            'phone_number', 'phone_is_verified'
        )
        ->where('phone_number', $request->phone_number)
        ->where('phone_is_verified', false)
        ->where('confirmation_code', $request->confirm_code)
        ->first();

        if($phoneToConfirm !== null){
            PhoneConfirmation::where('phone_number', $request->phone_number)
            ->where('phone_is_verified', false)
            ->where('confirmation_code', $request->confirm_code)
            ->update(
                ['phone_is_verified' => true]
            );
        } else {
            return false;
        }

        return true;

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

    public function sendConfirmationEmail(Request $request): bool{

        $request->validated();

        $system = 'external_mysql_' . $request->system;
        $lang = $request->lang;

        $user = array("email" => $request->email, "first_name" => $request->first_name);

        $systemHelper = new SystemHelper($request->system);

        $propertyRepository = new PropertyRepository($system);

        $propertyInfo = $propertyRepository->getPropertyInfoForEmail($request->property_id);

        $propertyInfo->system_domain = $systemHelper->systemDomain;
        $propertyInfo->system_name = $systemHelper->systemName;
        $propertyInfo->mailgun_domain = $systemHelper->mailgunDomain;

        $pin = mt_rand(100000, 999999);

        EmailConfirmation::updateOrCreate(
            [
                'email' => $request->email,
                'email_is_verified' => false
            ], [
                'confirmation_token' => $pin
        ]);


        // Setup your mailgun transport
        $client = new GuzzleClient();
        $transport = new MailgunTransport($client, config('services.mailgun.secret'), $propertyInfo->mailgun_domain);
        $mailer = new Swift_Mailer($transport);

        // Set the new mailer with the domain
        Mail::setSwiftMailer($mailer);

        Mail::queue(new EmailConfirmationEmail($user, $propertyInfo, $pin, $lang));

        return true;

    }

    public function validateEmailConfirmCode(Request $request): bool{

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
        } else
            return false;
        

        return true;

    }


    public function checkConfirmCode(Request $request){

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
