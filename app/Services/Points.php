<?php

namespace App\Services;

use App\Helpers\Sms\SmsAndCallTwimlHelper;
use App\Models\SpotbieUser;
use App\Models\SystemSms;
use App\Models\User;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class Points
{
    public function redeemedPoints(
        SpotbieUser $spotbieUser,
        User $user,
        SystemSms $sms,
        string $totalPoints,
        string $businessName,
        bool $withLoginInstructions
    ) {
        try
        {
            $lang = 'en';
            $sid = config('services.twilio.account_sid');
            $token = config('services.twilio.token');

            $client = new Client($sid, $token);
            $langHelper = new SmsAndCallTwimlHelper($lang);
            $body = $langHelper->getPointsRedeemedSmsTxt($totalPoints, $businessName, $withLoginInstructions, $user->email, $spotbieUser->first_name);

            $client->messages->create(
                $spotbieUser->phone_number,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $body,
                ]
            );

            // Update SMS message in DB;
            $sms->update([
                'sent' => true,
            ]);

            Log::info(
                '[Points]-[redeemedPoints]: Message Sent' .
                ', User ID: '. $user->id .
                ', Phone-Number: ' . $spotbieUser->phone_number .
                ', Business: ' . $businessName .
                ', Total Points: ' . $totalPoints
            );
        }
        catch(TwilioException $e)
        {
            $errorCode = '';
            switch($e->getCode())
            {
                case '21211':
                    $errorCode = 'phoneNumber.invalid';
                    break;
                case '21612':
                case '21408':
                case '21610':
                case '21614':
                    $errorCode = 'phoneNumber.unavailable';
                    break;
                default:
                    $errorCode = $e->getCode();
            }

            Log::error(
                '[Points]-[redeemedPoints]: Message Failed' .
                ', Phone-Number: ' . $spotbieUser->phone_number .
                ", Business: " . $businessName .
                ", Error Code: " . $errorCode .
                ", Error Message: " . $e->getMessage()
            );

            return $errorCode;
        }
    }

    public function sendBonusLp(
        SpotbieUser $spotbieUser,
        User $user,
        SystemSms $sms,
        string $totalPoints,
        string $businessName,
        string $range1,
        string $range2,
        string $range3,
        string $dayOfWeek
    ) {
        try
        {
            $lang = 'en';
            $sid = config('services.twilio.account_sid');
            $token = config('services.twilio.token');

            $client = new Client($sid, $token);
            $langHelper = new SmsAndCallTwimlHelper($lang);
            $body = $langHelper->getBonusLpSmsTxt(
                $totalPoints,
                $businessName,
                $spotbieUser->first_name,
                $range1,
                $range2,
                $range3,
                $dayOfWeek
            );

            $client->messages->create(
                $spotbieUser->phone_number,
                [
                    'from' => config('services.twilio.from'),
                    'body' => $body,
                ]
            );

            // Update SMS message in DB;
            $sms->update([
                'sent' => true,
            ]);

            Log::info(
                '[Points]-[redeemedPoints]: Message Sent' .
                ', User ID: '. $user->id .
                ', Phone-Number: ' . $spotbieUser->phone_number .
                ', Business: ' . $businessName .
                ', Total Points: ' . $totalPoints
            );
        }
        catch(TwilioException $e)
        {
            $errorCode = '';
            switch($e->getCode())
            {
                case '21211':
                    $errorCode = 'phoneNumber.invalid';
                    break;
                case '21612':
                case '21408':
                case '21610':
                case '21614':
                    $errorCode = 'phoneNumber.unavailable';
                    break;
                default:
                    $errorCode = $e->getCode();
            }

            Log::error(
                '[Points]-[redeemedPoints]: Message Failed' .
                ', Phone-Number: ' . $spotbieUser->phone_number .
                ", Business: " . $businessName .
                ", Error Code: " . $errorCode .
                ", Error Message: " . $e->getMessage()
            );

            return $errorCode;
        }
    }
}
