<?php

namespace App\Services\Sms;

interface ServiceTwilioSms
{
    public function sendVerificationCode(string $mobile, string $code);
}
