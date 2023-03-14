<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Notifications\NewUserInvite;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class RegisteredUserController extends Controller
{
    final public static function getPreferredCountries(): string
    {
        return env('PREFERRED_COUNTRIES', "US|IE|GB");
    }

    public function create()
    {
        return Inertia::render('Auth/Register', [
            'preferredCountries' => explode("|", self::getPreferredCountries())
        ]);
    }

    public function store(Request $request, CreatesNewUsers $creator): \Inertia\Response
    {
        event(new Registered($creator->create($request->all())));
        $user = User::where('email', $request['email'])->first();
        if ($user) {
            $token = Password::broker(config('fortify.passwords'))->createToken($user);
            $user->notify(new NewUserInvite($token));
        }
        return Inertia::render('Auth/Register', [
            'message' => (trans('auth.registration_successful')),
        ]);
    }
}
