<?php

namespace App\Http\Controllers\Marketplace;

use App\Enums\ModelsEnum;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Registration\Registration;
use App\Http\Controllers\VerifyMobileNumber\NewVerifyCodeRequest;
use App\Http\Controllers\VerifyMobileNumber\VerifyMobileNumber;
use App\Http\Controllers\VerifyMobileNumber\VerifyRequest;
use App\Models\Marketplace;
use App\Resources\MarketplaceResource;
use Illuminate\Support\Facades\Auth;

class MarketplaceController extends Controller
{
    public function __construct(
        private Registration $registration_service,
        private VerifyMobileNumber $verify_mobile_number,
    ) {
    }

    public function index()
    {
        return Marketplace::all();
    }

    public function store(MarketplaceRequest $request)
    {
        $data = [
            'national_id' => $request->input('national_id'),
            'name' => $request->input('name'),
            'mobile' => $request->input('mobile'),
            'location' => $request->input('location'),
            'password' => $request->input('password'),
        ];

        try {
            $this->registration_service->register(ModelsEnum::Marketplace, $data);

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

    public function login(MarketplaceRequest $request)
    {
        $marketplace = Marketplace::where('mobile', $request->input('mobile'))->first();

        if (! $marketplace || ! Auth::attempt(['mobile' => $request->input('mobile'), 'password' => $request->input('password')])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid login credentials',
            ], 401);
        }

        if (is_null($marketplace->mobile_verified_at)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Your mobile number is not verified',
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'id' => $marketplace->id,
                'name' => $marketplace->name,
                'mobile' => $marketplace->mobile,
                'token' => $marketplace->createToken('API TOKEN')->plainTextToken,
            ],
        ], 200);
    }

    public function addProduct()
    {
        $user = Auth::user();
        dd($user);
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
