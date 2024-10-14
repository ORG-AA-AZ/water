<?php

use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\User\UserController;
use App\Http\Middleware\EnsureMarketplaceOwner;
use App\Http\Middleware\EnsureMobileIsVerified;
use Illuminate\Support\Facades\Route;

// Public Routes For User
Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/verify-mobile', [UserController::class, 'verifyMobile']);
Route::post('/auth/resend-verify-code', [UserController::class, 'setNewVerifyCodeAndSendToUser']);
Route::post('/auth/login', [UserController::class, 'login']);


// Public Routes For Marketplace
Route::post('/auth/register', [MarketplaceController::class, 'store']);
Route::post('/auth/verify-mobile', [MarketplaceController::class, 'verifyMobile']);
Route::post('/auth/resend-verify-code', [MarketplaceController::class, 'setNewVerifyCodeAndSendToUser']);
Route::post('/auth/login', [MarketplaceController::class, 'login']);


// Protected Routes with 'auth:sanctum' middleware
Route::middleware('auth:sanctum')->group(function () {
    // Routes available to authenticated users
    Route::get('/marketplaces', [MarketplaceController::class, 'index']);

    // Mobile verification middleware
    Route::middleware([EnsureMobileIsVerified::class])->group(function () {
        Route::delete('/auth/logout', [UserController::class, 'logout']);
    });

    // Marketplace owner middleware
    Route::middleware([EnsureMarketplaceOwner::class])->group(function () {
        Route::get('/add-new-product', [MarketplaceController::class, 'addProduct']);
    });
});
