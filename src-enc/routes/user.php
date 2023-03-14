<?php

use App\Http\Controllers\User\ConfirmableTwoFactorAuthenticationController;
use App\Http\Controllers\User\ConfirmableTwoFactorAuthenticationStatusController;
use App\Http\Controllers\User\SetTwoFactorAuthenticationController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;


Route::prefix('/user')->middleware(['auth', 'verified'])->group(function () {

    Route::get('/set-two-factor-authentication', [SetTwoFactorAuthenticationController::class, 'show'])
        ->name('two-factor.set')->withoutMiddleware(['two-factor']);

    Route::get('/two-factor', [UserController::class, '__two_factor'])
        ->name('user.two-factor');

    Route::get('/password', [UserController::class, '__password'])
        ->name('user.password');

    Route::get('/confirm-two-factor-authentication-status', [ConfirmableTwoFactorAuthenticationStatusController::class, 'show'])
        ->name('two-factor.confirmation');

    Route::post('/confirm-two-factor-authentication', [ConfirmableTwoFactorAuthenticationController::class, 'store'])
        ->name('user-two-factor.confirm');

    Route::post('/two-factor-reset', [\App\Http\Controllers\Auth\ResetTwoFactorAuthenticationController::class, 'store'])
        ->name('user-two-factor.reset');

    Route::put('/profile-details',[UserController::class, '__update_profile'])
        ->name('user.update-profile');

    Route::get('/timezones',[UserController::class, 'timezones'])
        ->name('user.timezones');

});
