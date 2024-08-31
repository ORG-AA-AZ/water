<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->input('name'),
                'mobile' => $request->input('mobile'),
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Account registered success',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'mobile'=> $user->mobile,
                    'token' => $user->createToken('API TOKEN')->plainTextToken,
                ]
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 200);
        }

        // return new UserResource($user);
    }

    public function login(LoginRequest $request)
    {
        try {
            $user = User::where('mobile', $request->input('mobile'))->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User is not exist',
                    'data' => []
                ], 401);
            }

            if (!Auth::attempt(['mobile' => $request->input('mobile'), 'password' => $request->input('password')])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid login',
                    'data' => []
                ], 401);
            }

            $user->password = Hash::make($request->input('password'));

            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Login Success',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'mobile'=> $user->mobile,
                    'token' => $user->createToken('API TOKEN')->plainTextToken,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
