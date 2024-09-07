<?php

use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/verify-mobile', [UserController::class, 'verifyMobile']);
Route::post('/auth/resend-verify-code', [UserController::class, 'setNewVerifyCodeAndSendToUser']);
Route::post('/auth/login', [UserController::class, 'login']);

Route::post('/marketplace', [MarketplaceController::class, 'store']);

require __DIR__ . '/auth.php';
