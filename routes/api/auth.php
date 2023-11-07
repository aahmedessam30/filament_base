<?php

use App\Http\Controllers\Api\Authentication\AuthController;
use App\Http\Controllers\Api\Authentication\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------------------------------
 * | Authentication Routes (Api)
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register authentication routes for your application.
 * | These routes are loaded by the RouteServiceProvider and all of them will
 * | be assigned to the "api" middleware group. Make something great!
 * |
 * | Note: These routes are prefixed with /api/v1/auth
 */

Route::group(['middleware' => 'guest'], function () {
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('guest')
        ->name('register');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('guest')
        ->name('login');

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
        ->middleware('guest')
        ->name('password.email');

    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('guest')
        ->name('password.reset');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [VerifyEmailController::class, 'sendEmailVerificationNotification'])
        ->middleware(['throttle:6,1'])
        ->name('verification.send');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
