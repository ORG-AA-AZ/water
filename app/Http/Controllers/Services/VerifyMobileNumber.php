<?php

namespace App\Http\Controllers\Services;

use App\Enums\ModelsEnum;
use App\Http\Requests\NewVerifyCodeRequest;
use App\Http\Requests\VerifyRequest;
use App\Services\Sms\ServiceTwilioSms;

class VerifyMobileNumber
{
    public function __construct(private ServiceTwilioSms $sms_service)
    {
    }

    public function verifyMobile(ModelsEnum $model, VerifyRequest $request)
    {
        $entity = $model->value::where('mobile', $request->input('mobile'))->where('mobile_verification_code', $request->input('code'))->first();

        if (! $entity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid verification code',
            ], 422);
        }

        $entity->mobile_verified_at = now();
        $entity->update(['mobile_verification_code' => null]);

        return response()->json([
            'status' => 'success',
            'message' => 'Mobile number verified successfully',
        ], 200);
    }

    public function setNewVerificationCode(ModelsEnum $model, NewVerifyCodeRequest $request)
    {
        $entity = $model->value::where('mobile', $request->input('mobile'))->first();

        if (! $entity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Entity not found',
            ], 404);
        }

        $verification_code = rand(100000, 999999);
        $entity->update(['mobile_verification_code' => $verification_code]);

        $this->sms_service->sendVerificationCode($entity->mobile, $verification_code);

        return response()->json([
            'status' => 'success',
            'message' => 'New verification code sent successfully.',
        ], 200);
    }
}
