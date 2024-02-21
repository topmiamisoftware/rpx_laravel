<?php

namespace App\Helpers\Sms;

class SmsAndCallTwimlHelper
{
    public function __construct(public string $twimlLocale)
    {
    }

    public function getConfirmationCallSpeech($confirmationCode)
    {
        $confirmationCode = implode('. ', str_split($confirmationCode));

        $greeting = trans('phoneConfirmation.greeting');
        $codeOne = trans('phoneConfirmation.code_1', ['confirmationCode' => $confirmationCode]);
        $codeTwo = trans('phoneConfirmation.code_2', ['confirmationCode' => $confirmationCode]);

        $locale = $this->twimlLocale;

        $text = "<Response><Pause length='3'/>
        <Say language='$locale'>$greeting</Say>
        <Say language='$locale'>$codeOne</Say>
        <Say language='$locale'>$codeTwo</Say>
        </Response>";

        return $text;
    }

    public function getConfirmPhoneSmsText($confirmationCode)
    {
        $smsText = trans('phoneConfirmation.smsText', ['confirmationCode' => $confirmationCode]);

        return $smsText;
    }

    public function getPromotionalSmsText(string $firstName, string $businessName, string $body): string
    {
        return trans('promotional_sms.promotional_text', ['firstName' => $firstName, 'businessName' => $businessName, 'body' => $body]);
    }

    public function getSettingsSmsText(string $firstName)
    {
        return trans('promotional_sms.smsOptInText', ['firstName' => $firstName]);
    }
}
