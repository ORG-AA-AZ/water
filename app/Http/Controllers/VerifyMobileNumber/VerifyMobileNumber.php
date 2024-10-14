<?php

namespace App\Http\Controllers\VerifyMobileNumber;

use App\Enums\ModelsEnum;
use App\Services\Sms\ServiceTwilioSms;

class VerifyMobileNumber
{
    public function __construct(private ServiceTwilioSms $sms_service)
    {}

    public function verifyMobile(VerifyRequest $request, ModelsEnum $model)
    {
        $entity = $this->getEntityByMobileAndCode($model, $request->input('mobile'), $request->input('code'));

        if (! $entity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid verification code',
            ], 422);
        }

        $entity->mobile_verified_at = now();
        $entity->mobile_verification_code = null;
        $entity->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Mobile number verified successfully',
        ], 200);
    }

    public function setNewVerificationCode(NewVerifyCodeRequest $request, ModelsEnum $model)
    {
        $entity = $this->getEntityById($model, $request->input('user_id'));

        if (! $entity) {
            return response()->json([
                'status' => 'error',
                'message' => 'Entity not found',
            ], 404);
        }

        $verification_code = rand(100000, 999999);
        $entity->mobile_verification_code = $verification_code;
        $entity->save();

        $this->sms_service->sendVerificationCode($entity->mobile, $verification_code);

        return response()->json([
            'status' => 'success',
            'message' => 'New verification code sent successfully.',
        ], 200);
    }

    private function getEntityByMobileAndCode(ModelsEnum $model, $mobile, $code)
    {
        $model_class = $model->getModel();

        return $model_class::where('mobile', $mobile)
            ->where('mobile_verification_code', $code)
            ->first();
    }

    private function getEntityById(ModelsEnum $model, $id)
    {
        $model_class = $model->getModel();

        return $model_class::find($id);
    }
}
