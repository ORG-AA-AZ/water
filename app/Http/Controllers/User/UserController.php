<?php

namespace App\Http\Controllers\User;

use App\Enums\ModelsEnum;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Registration\Registration;
use App\Http\Controllers\VerifyMobileNumber\NewVerifyCodeRequest;
use App\Http\Controllers\VerifyMobileNumber\VerifyMobileNumber;
use App\Http\Controllers\VerifyMobileNumber\VerifyRequest;
use App\Models\User;
use App\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        private VerifyMobileNumber $verify_mobile_number,
        private Registration $registration_service,
    ){
    }

    public function register(RegisterRequest $request)
    {
        $data = [
            'name' => $request->input('name'),
            'mobile' => $request->input('mobile'),
            'password' => $request->input('password'),
        ];

        try {
            $this->registration_service->register(ModelsEnum::User, $data);

            return response()->json([
                'status' => 'success',
                'message' => 'Account registered successfully. Verify your mobile number',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        }
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
