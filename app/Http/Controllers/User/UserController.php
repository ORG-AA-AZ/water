<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::where('mobile', $request->input('mobile'))->first();

            if ($user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User is already exist',
                    'data' => [],
                ], 401);
            }

            $verification_code = rand(100000, 999999);

            $user = User::create([
                'name' => $request->input('name'),
                'mobile' => $request->input('mobile'),
                'password' => Hash::make($request->password),
                'mobile_verification_code' => $verification_code,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Account registered success. Verify your mobile',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'mobile' => $user->mobile,
                    'token' => $user->createToken('API TOKEN')->plainTextToken,
                ],
            ], 401);

            $this->sendSms($request->input('mobile'), $verification_code);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    private function sendSms(string $mobile, string $code)
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.from');

        try {
            $client = new Client($sid, $token);

            $client->messages->create($mobile, [
                'from' => $from,
                'body' => "Your verification code is: {$code}",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 200);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('mobile', $request->input('mobile'))->first();

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User does not exist',
                    'data' => [],
                ], 401);
            }

            if (! Auth::attempt(['mobile' => $request->input('mobile'), 'password' => $request->input('password')])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid login',
                    'data' => [],
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
                'message' => 'Login Success',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'mobile' => $user->mobile,
                    'token' => $user->createToken('API TOKEN')->plainTextToken,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        }
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

    public function setNewVerifyCodeAndSendToUser(NewVerifyCodeRequest $request)
    {
        $user = User::where('id', $request->input('user_id'))->first();

        $user->update([
            'mobile_verification_code' => $verification_code = rand(100000, 999999)
        ]);

        $this->sendSms($user->mobile, $verification_code);

        return response()->json([
            'status' => 'success',
            'message' => 'New verify number was sent.',
        ], 200);
    }
}
