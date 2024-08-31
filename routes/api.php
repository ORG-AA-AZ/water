<?php

use App\Http\Controllers\Marketplace\MarketplaceController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [UserController::class, 'register']);
Route::post('/auth/login', [UserController::class, 'login']);
Route::delete('/auth/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/marketplace', [MarketplaceController::class, 'store']);
