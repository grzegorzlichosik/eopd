<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [CustomAuthController::class, 'index'])->name('index');
Route::post('registration', [CustomAuthController::class, 'postRegistration'])->name('postRegistration');
Route::get('login', [CustomAuthController::class, 'getLogin'])->name('login');
Route::post('login', [CustomAuthController::class, 'postLogin'])->name('postLogin');

Route::get('logout', [CustomAuthController::class, 'signOut'])->middleware(['auth'])->name('logout');
Route::get('signout', [CustomAuthController::class, 'signOut'])->middleware(['auth'])->name('signout');
Route::get('exercise', [CustomAuthController::class, 'exercise'])->middleware(['auth'])->name('exercise');
Route::post('voice-analyst', [CustomAuthController::class, 'postVoiceAnalyst'])->middleware(['auth'])->name('postVoiceAnalyst');

