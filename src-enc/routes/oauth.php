<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Platform\NylasLinkController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/resources/oauth/callback', [\App\Http\Controllers\Calendar\OAuthController::class, 'resourcesCallback'])->name('resources.oauth.callback');
    Route::get('/calendar/oauth/callback', [\App\Http\Controllers\Calendar\OAuthController::class, 'calendarCallback'])->name('calendar.oauth.callback');
    Route::get('/master/oauth/callback', [NylasLinkController::class, 'getHostedAuthToken']);
});

