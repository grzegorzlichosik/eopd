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
Route::prefix('resources')
    ->middleware(['auth', 'verified'])->group(function () {
        Route::get('/link/native/init', [\App\Http\Controllers\Resources\NylasLinkController::class, 'authNative'])->name('resources.init');
        Route::get('/link/retry', [\App\Http\Controllers\Resources\NylasLinkController::class, 'retry'])->name('resources.retry');
    });
