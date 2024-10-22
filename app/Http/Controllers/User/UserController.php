<?php

namespace App\Http\Controllers\User;

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

class UserController extends BaseAuthController
{
    public function __construct(
        private Location $location,
        private VerifyMobileNumber $verify_mobile_number,
        LoginAndRegisterService $service
    ) {
        parent::__construct($service);
    }

    public function registerUser(UserRegisterRequest $request)
    {
        return parent::register(ModelsEnum::User, $request);
    }

    public function loginUser(LoginRequest $request)
    {
        return parent::login(ModelsEnum::User, $request);
    }

    public function resetUserPassword(ResetPasswordRequest $request)
    {
        return parent::resetPassword(ModelsEnum::User, $request);
    }

    public function forgetUserPassword(ForgetPasswordRequest $request)
    {
        return parent::forgetPassword(ModelsEnum::User, $request);
    }

    public function logoutUser()
    {
        return parent::logout();
    }

    public function verifyMobile(VerifyRequest $request)
    {
        return $this->verify_mobile_number->verifyMobile(ModelsEnum::User, $request);
    }

    public function resendVerificationCode(NewVerifyCodeRequest $request)
    {
        return $this->verify_mobile_number->setNewVerificationCode(ModelsEnum::User, $request);
    }

    public function changeLocation(ChangeLocationRequest $request): void
    {
        $this->location->changeLocation(ModelsEnum::User, $request);
    }
}
