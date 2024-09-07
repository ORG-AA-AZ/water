<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $sms_service;

    public function __construct(SmsService $sms_service)
    {
        $this->sms_service = $sms_service;
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
        $user = User::where('mobile', $request->input('mobile'))
            ->where('mobile_verification_code', $request->input('code'))
            ->first();

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid verification code',
            ], 422);
        }

        $user->mobile_verified_at = now();
        $user->mobile_verification_code = null;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Mobile number verified successfully',
        ], 200);
    }

    public function setNewVerificationCode(NewVerifyCodeRequest $request)
    {
        $user = User::find($request->input('user_id'));

        if (! $user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }

        $verification_code = rand(100000, 999999);
        $user->mobile_verification_code = $verification_code;
        $user->save();

        $this->sms_service->sendVerificationCode($user->mobile, $verification_code);

        return response()->json([
            'status' => 'success',
            'message' => 'New verification code sent successfully.',
        ], 200);
    }
}
