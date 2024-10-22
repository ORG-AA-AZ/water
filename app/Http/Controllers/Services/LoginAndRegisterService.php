<?php

namespace App\Http\Controllers\Services;

use App\Enums\ModelsEnum;
use App\Services\Sms\ServiceTwilioSms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        if (! $entity) {
            throw new \Exception('Mobile number is not registered');
        }

        if (is_null($entity->mobile_verified_at)) {
            throw new \Exception('Your mobile number is not verified');
        }

        $login_using_password = Auth::attempt(['mobile' => $data['mobile'], 'password' => $data['password']]);
        $login_using_reset_password = Hash::check($data['password'], $entity->reset_password);

        if (! $login_using_password && ! $login_using_reset_password) {
            throw new \Exception('Invalid login credentials');
        }

        return $entity;
    }

    public function resetPassword(ModelsEnum $model, array $data)
    {
        $model->value::where('mobile', $data['mobile'])->first()->update(['password' => $data['password']]);
    }

    public function forgetPassword(ModelsEnum $model, array $data)
    {
        $entity = $model->value::where('mobile', $data['mobile'])->first();

        if (! $entity) {
            throw new \Exception('Your mobile number is not registered');
        }

        $entity->update(['reset_password' => $reset_password = Str::random(10)]);

        $this->sms_service->sendNewPassword($entity->mobile, Hash::make($reset_password));
    }

    private function prepareData(array $data): array
    {
        $verification_code = rand(100000, 999999);

        $retrieved_data = [
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'password' => Hash::make($data['password']),
            'mobile_verification_code' => $verification_code,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
        ];

        // Add marketplace-specific fields
        if (isset($data['national_id'])) {
            $retrieved_data['national_id'] = $data['national_id'];
        }

        return $retrieved_data;
    }
}
