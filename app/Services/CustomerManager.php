<?php

namespace App\Services;

use App\Helpers\Sms\SmsAndCallTwimlHelper;
use App\Models\Sms;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class CustomerManager
{
    public function sendSms(string $userPhoneNumber, string $userID, string $firstName, string $businessName, Sms $sms) {
        try
        {
            $lang = 'en';
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');

            $client = new Client($sid, $token);
            $langHelper = new SmsAndCallTwimlHelper($lang);
            $body = $langHelper->getPromotionalSmsText($firstName, $businessName);
            $body .= $sms->body;

            $client->messages->create(
                $userPhoneNumber,
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
                '[CustomerManager]-[sendSms]: User ID: '. $userID .
                ', Phone-Number: ' . $userPhoneNumber .
                ', Business: ' . $businessName
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
                '[CustomerManager]-[sendSms]: Phone-Number: ' .
                $userPhoneNumber .
                ", Business: " . $businessName .
                ", Error Code: " . $errorCode
            );

            return $errorCode;
        }
    }
}
