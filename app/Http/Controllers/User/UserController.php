<?php

namespace App\Http\Controllers\User;

use App\Enums\ModelsEnum;
use App\Http\Controllers\Controller;
use App\Http\Controllers\VerifyMobileNumber\NewVerifyCodeRequest;
use App\Http\Controllers\VerifyMobileNumber\VerifyMobileNumber;
use App\Http\Controllers\VerifyMobileNumber\VerifyRequest;
use App\Models\User;
use App\Services\Sms\ServiceTwilioSms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        private VerifyMobileNumber $verify_mobile_number,
        protected ServiceTwilioSms $sms_service,
    ) {
    }

    public function register(RegisterRequest $request)
    {
        $user = User::where('mobile', $request->input('mobile'))->first();

        if ($user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User already exists',
            ], 401);
        }

        $verification_code = rand(100000, 999999);

        $user = User::create([
            'name' => $request->input('name'),
            'mobile' => $request->input('mobile'),
            'password' => Hash::make($request->password),
            'mobile_verification_code' => $verification_code,
        ]);

        // Send SMS verification code
        $this->sms_service->sendVerificationCode($user->mobile, $verification_code);

        return response()->json([
            'status' => 'success',
            'message' => 'Account registered successfully. Verify your mobile number',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'token' => $user->createToken('API TOKEN')->plainTextToken,
            ],
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('mobile', $request->input('mobile'))->first();

        if (! $user || ! Auth::attempt(['mobile' => $request->input('mobile'), 'password' => $request->input('password')])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid login credentials',
            ], 401);
        }

        if (is_null($user->mobile_verified_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your mobile number is not verified',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'mobile' => $user->mobile,
                'token' => $user->createToken('API TOKEN')->plainTextToken,
            ],
        ], 200);
    }

    public function logout()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user) {
            $user->tokens()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully.',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'User not authenticated',
        ], 401);
    }

    public function verifyMobile(VerifyRequest $request)
    {
        return $this->verify_mobile_number->verifyMobile($request, ModelsEnum::User);
    }

    public function resendVerificationCode(NewVerifyCodeRequest $request)
    {
        return $this->verify_mobile_number->setNewVerificationCode($request, ModelsEnum::User);
    }
}
