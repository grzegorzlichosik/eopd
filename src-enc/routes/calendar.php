<?php

use Illuminate\Support\Facades\Route;

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
    Route::get('/calendar/link/native/init', [\App\Http\Controllers\Calendar\NylasLinkController::class, 'authNative'])->name('calendar.init');
    Route::get('/calendar', [\App\Http\Controllers\Calendar\CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/link/retry', [\App\Http\Controllers\Calendar\NylasLinkController::class, 'retry'])->name('calendar.retry');
});

