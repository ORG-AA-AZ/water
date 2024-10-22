<?php

namespace App\Http\Controllers\Marketplace;

use App\Enums\ModelsEnum;
use App\Http\Controllers\BaseAuthController;
use App\Http\Controllers\LoginAndRegisterService\LoginAndRegisterService;
use App\Http\Controllers\VerifyMobileNumber\VerifyMobileNumber;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\NewVerifyCodeRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyRequest;

class MarketplaceController extends BaseAuthController
{
    public function __construct(
        private VerifyMobileNumber $verify_mobile_number,
        LoginAndRegisterService $service
    ) {
        parent::__construct($service);
    }

    public function registerMarketplace(MarketplaceRegisterRequest $request)
    {
        $data = [
            'national_id' => $request->input('national_id'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
        ];

        return parent::register(ModelsEnum::Marketplace, $request, $data);
    }

    public function loginMarketplace(LoginRequest $request)
    {
        return parent::login(ModelsEnum::Marketplace, $request);
    }

    public function resetMarketplacePassword(ResetPasswordRequest $request)
    {
        return parent::resetPassword(ModelsEnum::Marketplace, $request);
    }

    public function forgetMarketplacePassword(ForgetPasswordRequest $request)
    {
        return parent::forgetPassword(ModelsEnum::Marketplace, $request);
    }

    public function logoutMarketplace()
    {
        return parent::logout();
    }

    public function verifyMobile(VerifyRequest $request)
    {
        return $this->verify_mobile_number->verifyMobile(ModelsEnum::Marketplace, $request);
    }

    public function resendVerificationCode(NewVerifyCodeRequest $request)
    {
        return $this->verify_mobile_number->setNewVerificationCode(ModelsEnum::Marketplace, $request);
    }
}
