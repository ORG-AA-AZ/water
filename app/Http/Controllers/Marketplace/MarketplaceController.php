<?php

namespace App\Http\Controllers\Marketplace;

use App\Enums\ModelsEnum;
use App\Http\Controllers\BaseAuthController;
use App\Http\Controllers\Services\Location;
use App\Http\Controllers\Services\LoginAndRegisterService;
use App\Http\Controllers\Services\VerifyMobileNumber;
use App\Http\Requests\ChangeLocationRequest;
use App\Http\Requests\ForgetPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\NewVerifyCodeRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyRequest;

class MarketplaceController extends BaseAuthController
{
    public function __construct(
        private Location $location,
        private VerifyMobileNumber $verify_mobile_number,
        LoginAndRegisterService $service
    ) {
        parent::__construct($service);
    }

    public function registerMarketplace(MarketplaceRegisterRequest $request)
    {
        $data = [
            'national_id' => $request->input('national_id'),
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

    public function changeLocation(ChangeLocationRequest $request): void
    {
        $this->location->changeLocation(ModelsEnum::Marketplace, $request);
    }
}
