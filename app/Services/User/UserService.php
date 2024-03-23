<?php

namespace App\Services\User;

use App\Helpers\Sms\SmsAndCallTwimlHelper;
use App\Models\SpotbieUser;
use App\Models\SystemSms;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function sendSettingsSms(
        string $userPhoneNumber,
        string $userID,
        SpotbieUser $spotbieUser,
        SystemSms $sms,
    )
    {
        $userPhoneNumber = '+1' . $userPhoneNumber;
        try {
            $lang = 'en';
            $sid = config('services.twilio.account_sid');
            $token = config('services.twilio.token');

            $client = new Client($sid, $token);
            $langHelper = new SmsAndCallTwimlHelper($lang);
            $body = $langHelper->getSettingsSmsText($spotbieUser->first_name);
            $body .= $sms->body;

            if ($spotbieUser->sms_opt_in === 0) {
                $client->messages->create(
                    $userPhoneNumber,
                    [
                        'from' => config('services.twilio.from'),
                        'body' => $body,
                    ]
                );

                Log::info(
                    '[UserService]-[sendSettingsSms]: Message Sent' .
                    ', User ID: ' . $userID .
                    ', Phone-Number: ' . $userPhoneNumber
                );
            }

            // Update SMS message in DB;
            $sms->update([
                'sent' => true,
            ]);

            $spotbieUser->update([
                'sms_opt_in' => true,
                'phone_number' => $userPhoneNumber
            ]);

            Log::info(
                '[UserService]-[sendSettingsSms]: Phone Number Updated ' .
                ', User ID: ' . $userID .
                ', Phone-Number: ' . $userPhoneNumber
            );
        } catch (TwilioException $e) {
            $errorCode = '';
            switch ($e->getCode()) {
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
                '[UserService]-[sendSettingsSms]: Message Failed' .
                ', Phone-Number: ' . $userPhoneNumber .
                ", Error Code: " . $errorCode .
                ", Error Message: " . $e->getMessage()
            );

            return $errorCode;
        }
    }
}
