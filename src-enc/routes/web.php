<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
});

Route::post('/iframe', [App\Http\Controllers\IframeController::class, 'store'])->name('iframe');

require('auth.php');
require('user.php');
require('admin.php');
require('platform.php');
require('calendar.php');
require('oauth.php');
require('resources.php');
require('encounters.php');
require('agent.php');
require('superAdmin.php');
