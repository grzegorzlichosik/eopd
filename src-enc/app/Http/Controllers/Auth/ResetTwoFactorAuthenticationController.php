<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use App\Notifications\Reset2fa;
use App\Notifications\Reset2FAAdmin;
use App\Notifications\Reset2FAPassword;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;

class ResetTwoFactorAuthenticationController extends Controller
{
    public function store(TwoFactorLoginRequest $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if (Auth::user()) {
            $user = Auth::user();
        } else {
            $user = $request->challengedUser();
        }

        $user->setTwoFactorResetRequest();
        $token = Password::broker(config('fortify.passwords'))->createToken($user);
        $user->notify(new Reset2fa($token));

        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : Redirect::route('login')->with('status', trans('auth.reset-two-factor-success'));

    }
}
