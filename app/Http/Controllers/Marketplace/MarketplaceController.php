<?php

namespace App\Http\Controllers\Marketplace;

use App\Enums\ModelsEnum;
use App\Http\Controllers\BaseAuthController;
use App\Http\Controllers\LoginAndRegisterService\LoginAndRegisterService;
use App\Http\Controllers\VerifyMobileNumber\NewVerifyCodeRequest;
use App\Http\Controllers\VerifyMobileNumber\VerifyMobileNumber;
use App\Http\Controllers\VerifyMobileNumber\VerifyRequest;

class MarketplaceController extends BaseAuthController
{
    public function __construct(
        private VerifyMobileNumber $verify_mobile_number,
        LoginAndRegisterService $service
    ) {
        parent::__construct($service);
    }

    public function registerMarketplace(MarketplaceRequest $request)
    {
        $data = [
            'national_id' => $request->input('national_id'),
            'location' => $request->input('location'),
        ];

        return parent::register(ModelsEnum::Marketplace, $request, $data);
    }

    public function loginMarketplace(MarketplaceRequest $request)
    {
        return parent::login(ModelsEnum::Marketplace, $request);
    }

    public function resetMarketplacePassword(MarketplaceRequest $request)
    {
        return parent::resetPassword(ModelsEnum::Marketplace, $request);
    }

    public function logoutMarketplace()
    {
        return parent::logout();
    }

    public function verifyMobile(VerifyRequest $request)
    {
        return $this->verify_mobile_number->verifyMobile($request, ModelsEnum::Marketplace);
    }

    public function resendVerificationCode(NewVerifyCodeRequest $request)
    {
        return $this->verify_mobile_number->setNewVerificationCode($request, ModelsEnum::Marketplace);
    }
}
