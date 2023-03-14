<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\ConfirmableTwoFactorAuthenticationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse;
use Laravel\Fortify\Events\RecoveryCodeReplaced;
use Laravel\Fortify\Fortify;

class ConfirmableTwoFactorAuthenticationController extends Controller
{
    public function store(
        ConfirmableTwoFactorAuthenticationRequest $request
    ): JsonResponse|RedirectResponse|FailedTwoFactorLoginResponse
    {
        $user = $request->user();
        if ($code = $request->validRecoveryCode()) {
            $user->replaceRecoveryCode($code);

            event(new RecoveryCodeReplaced($user, $code));
        } elseif (!$request->hasValidCode()) {
            return app(FailedTwoFactorLoginResponse::class);
        }

        $request->session()->put('auth.two_factor_authentication_confirmed_at', time());

        return $request->wantsJson()
            ? new JsonResponse('', 201)
            : redirect()->intended(Fortify::redirects('two-factor-authentication-confirmation'));
    }
}
