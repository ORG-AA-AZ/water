<?php

namespace App\Http\Controllers\Registration;

use App\Enums\ModelsEnum;
use App\Http\Controllers\VerifyMobileNumber\VerifyMobileNumber;
use App\Services\Sms\ServiceTwilioSms;
use Illuminate\Support\Facades\Hash;

class Registration
{
    public function __construct(
        private VerifyMobileNumber $verify_mobile_number,
        protected ServiceTwilioSms $sms_service,
    ) {
    }

    public function register(ModelsEnum $model, array $data)
    {
        $verification_code = rand(100000, 999999);
        $retrived_data = array_merge($data, [
            'password' => Hash::make($data['password']),
            'mobile_verification_code' => $verification_code,
        ]);

        $new = $model->value::firstOrCreate(
            ['mobile' => $data['mobile']],
            $retrived_data
        );

        // Send SMS verification code
        $this->sms_service->sendVerificationCode($new->mobile, $verification_code);

        return $new;
    }
}
