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

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');
Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? back()->with(['status' => __($status)])
        : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');
