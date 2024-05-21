<?php

namespace App\Services;

use App\Helpers\Sms\SmsAndCallTwimlHelper;
use App\Models\SpotbieUser;
use App\Models\SystemSms;
use App\Models\User;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class Rewards
{
    public function redeemedSms(
        SpotbieUser $spotbieUser,
        User $user,
        SystemSms $sms,
        string $rewardName,
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
            $body = $langHelper->getRewardRedeemedSmsTxt($rewardName, $businessName, $withLoginInstructions, $user->email, $spotbieUser->first_name);

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
                '[Rewards]-[redeemedSms]: Message Sent' .
                ', User ID: '. $user->id .
                ', Phone-Number: ' . $spotbieUser->phone_number .
                ', Business: ' . $businessName .
                ', Reward Name: ' . $rewardName
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
                '[Rewards]-[redeemedSms]: Message Failed' .
                ', Phone-Number: ' . $spotbieUser->phone_number .
                ", Business: " . $businessName .
                ", Error Code: " . $errorCode .
                ", Error Message: " . $e->getMessage()
            );

            return $errorCode;
        }
    }
}
