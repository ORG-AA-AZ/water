<?php

use App\Http\Controllers\User\UserController;
use App\Http\Middleware\EnsureMobileIsVerified;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware([EnsureMobileIsVerified::class])->group(function () {
        Route::delete('/auth/logout', [UserController::class, 'logout']);
    });
});
