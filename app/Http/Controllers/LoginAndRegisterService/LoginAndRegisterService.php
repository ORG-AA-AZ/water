<?php

namespace App\Http\Controllers\LoginAndRegisterService;

use App\Enums\ModelsEnum;
use App\Http\Controllers\VerifyMobileNumber\VerifyMobileNumber;
use App\Services\Sms\ServiceTwilioSms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginAndRegisterService
{
    public function __construct(
        private VerifyMobileNumber $verify_mobile_number,
        protected ServiceTwilioSms $sms_service,
    ) {
    }
    public function register(ModelsEnum $model, array $data): void
    {
        $retrieved_data = $this->prepareData($data);

        $entity = $model->value::create($retrieved_data);

        // Send SMS verification code
        $this->sms_service->sendVerificationCode($entity->mobile, $retrieved_data['mobile_verification_code']);
    }

    public function login(ModelsEnum $model, array $data)
    {
        $entity = $model->value::where('mobile', $data['mobile'])->first();

        if (! $entity || ! Auth::attempt(['mobile' => $data['mobile'], 'password' => $data['password']])) {
            throw new \Exception('Invalid login credentials');
        }

        if (is_null($entity->mobile_verified_at)) {
            throw new \Exception('Your mobile number is not verified');
        }

        return $entity;
    }

    public function resetPassword(ModelsEnum $model, array $data)
    {
        $model->value::where('mobile', $data['mobile'])->first()->update(['password' => $data['password']]);
    }

    private function prepareData(array $data): array
    {
        $verification_code = rand(100000, 999999);

        $retrieved_data = [
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'password' => Hash::make($data['password']),
            'mobile_verification_code' => $verification_code,
        ];

        // Add marketplace-specific fields
        if (isset($data['national_id']) && isset($data['location'])) {
            $retrieved_data['national_id'] = $data['national_id'];
            $retrieved_data['location'] = $data['location'];
        }

        return $retrieved_data;
    }
}
