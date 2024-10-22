<?php

namespace App\Http\Controllers;

use App\Enums\ModelsEnum;
use App\Http\Controllers\Services\LoginAndRegisterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

abstract class BaseAuthController extends Controller
{
    public function __construct(
        private LoginAndRegisterService $service
    ) {
    }

    public function register(ModelsEnum $model, $request, array $model_specific_fields = []): JsonResponse
    {
        $data = array_merge(
            $request->only(['name', 'mobile', 'password', 'latitude', 'longitude']),
            $model_specific_fields
        );

        try {
            $this->service->register($model, $data);

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

    public function login(ModelsEnum $model, $request): JsonResponse
    {
        $data = $request->only(['mobile', 'password']);

        try {
            $entity = $this->service->login($model, $data);

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'id' => $entity->id,
                    'name' => $entity->name,
                    'mobile' => $entity->mobile,
                    'token' => $entity->createToken('API TOKEN')->plainTextToken,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function resetPassword(ModelsEnum $model, $request): JsonResponse
    {
        $data = $request->only(['mobile', 'password']);

        try {
            $this->service->resetPassword($model, $data);

            return response()->json([
                'status' => 'success',
                'message' => 'Password reset successful',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function forgetPassword(ModelsEnum $model, $request): JsonResponse
    {
        $data = $request->input('mobile');

        try {
            $this->service->resetPassword($model, $data);

            return response()->json([
                'status' => 'success',
                'message' => 'Login using new password that sent to mobile.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function logout(): JsonResponse
    {
        /** @var Tokenable $entity */
        $entity = Auth::user();

        $entity->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully.',
        ], 200);
    }
}
