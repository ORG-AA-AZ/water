<?php

namespace App\Services\Sms;

interface ServiceTwilioSms
{
    public function sendVerificationCode(string $mobile, string $code);

    public function sendNewPassword(string $mobile, string $new_password);
}
