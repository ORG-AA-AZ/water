<?php

use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\User\UserController;
use App\Http\Middleware\EnsureMarketplaceOwner;
use App\Http\Middleware\EnsureMobileIsVerified;
use Illuminate\Support\Facades\Route;

// Public Routes For User
Route::post('/auth/user-register', [UserController::class, 'registerUser']);
Route::post('/auth/user-reset-password', [UserController::class, 'resetUserPassword']);
Route::post('/auth/user-forget-password', [UserController::class, 'forgetUserPassword']);
Route::post('/auth/user-verify-mobile', [UserController::class, 'verifyMobile']);
Route::post('/auth/user-resend-verify-code', [UserController::class, 'resendVerificationCode']);
Route::post('/auth/user-login', [UserController::class, 'loginUser']);

// Public Routes For Marketplace
Route::post('/auth/marketplace-register', [MarketplaceController::class, 'registerMarketplace']);
Route::post('/auth/marketplace-reset-password', [MarketplaceController::class, 'resetMarketplacePassword']);
Route::post('/auth/marketplace-verify-mobile', [MarketplaceController::class, 'verifyMobile']);
Route::post('/auth/marketplace-resend-verify-code', [MarketplaceController::class, 'setNewVerifyCodeAndSendToUser']);
Route::post('/auth/marketplace-login', [MarketplaceController::class, 'loginMarketplace']);

// Protected Routes with 'auth:sanctum' middleware
Route::middleware('auth:sanctum')->group(function () {
    // Routes available to authenticated users
    Route::get('/marketplaces', [MarketplaceController::class, 'index']);

    Route::middleware([EnsureMobileIsVerified::class])->group(function () {
        Route::delete('/auth/user-logout', [UserController::class, 'logoutUser']);
        Route::delete('/auth/marketplace-logout', [MarketplaceController::class, 'logoutMarketplace']);
    });

    // Marketplace owner middleware
    Route::middleware([EnsureMarketplaceOwner::class])->group(function () {
        Route::get('/add-new-product', [MarketplaceController::class, 'addProduct']);
    });
});
