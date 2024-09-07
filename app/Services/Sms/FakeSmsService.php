<?php

namespace App\Services\Sms;

class FakeSmsService implements ServiceTwilioSms
{
    public function sendVerificationCode(string $mobile, string $code)
    {
        return response()->json([], 201);
    }
}
