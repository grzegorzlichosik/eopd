<?php

use App\Http\Controllers\Auth\ResetTwoFactorAuthenticationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\TwoFactorAuthenticatedSessionController;
use App\Http\Controllers\Auth\AccountActivationController;


/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
|
*/
Route::post('/reset-two-factor-authentication', [ResetTwoFactorAuthenticationController::class, 'store'])
    ->name('two-factor.reset');

Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
    ->name('two-factor.login');

Route::get('/two-factor-challenge/{uuid}/{code}', [TwoFactorAuthenticatedSessionController::class, 'emailRecoveryCode'])
    ->name('mail.recovery.codes');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->name('password.email');

Route::get('/register', [RegisteredUserController::class, 'create'])
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->name('register');

Route::get('/resend-activation', [AccountActivationController::class, 'index'])
    ->name('resend.activation');

Route::post('/resend-activation', [AccountActivationController::class, 'store'])
    ->name('resend.activation');
