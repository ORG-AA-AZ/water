<?php

use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\User\UserController;
use App\Http\Middleware\EnsureMobileIsVerified;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/verify-mobile', [UserController::class, 'verifyMobile']);
Route::post('/auth/resend-verify-code', [UserController::class, 'setNewVerifyCodeAndSendToUser']);
Route::post('/auth/login', [UserController::class, 'login']);

Route::post('/marketplace', [MarketplaceController::class, 'store']);

Route::middleware(['auth:sanctum', EnsureMobileIsVerified::class])->group(function () {
    Route::delete('/auth/logout', [UserController::class, 'logout']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/test', function(){
        dd('testing');
    });
});
