<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Notifications\RecoveryCode;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Laravel\Fortify\Events\RecoveryCodeReplaced;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;
use \Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController as AuthenticatedSessionController;

class TwoFactorAuthenticatedSessionController extends AuthenticatedSessionController
{
    private const FAILED_2FA_COUNTER = 5;

    public function store(TwoFactorLoginRequest $request)
    {

        $isRecoveryMode = $request->get('is_recovery_mode');
        $user = $request->challengedUser();

        /**
         * Always check if account is locked
         */
        if ($user->failed_2fa_counter >= self::FAILED_2FA_COUNTER) {
            throw ValidationException::withMessages(['account_locked' => true]);
        }

        if ($isRecoveryMode) {
            if (!$request->recovery_code) {
                throw ValidationException::withMessages(['recovery_code' => trans('errors.recovery_code_required')]);
            }

            if ($code = $request->validRecoveryCode()) {
                $user->replaceRecoveryCode($code);
                event(new RecoveryCodeReplaced($user, $code));
            } else {
                $user->increment('failed_2fa_counter');
                throw ValidationException::withMessages(['recovery_code' => trans('errors.invalid_recovery_code')]);
            }
        } else {
            if (!$request->code) {
                throw ValidationException::withMessages(['code' => trans('errors.2fa_code_required')]);
            }

            if (!$request->hasValidCode() && $user->failed_2fa_counter < self::FAILED_2FA_COUNTER) {
                $user->increment('failed_2fa_counter');
                throw ValidationException::withMessages(['code' => trans('errors.invalid_2fa_code')]);
            }
        }

        $user->failed_2fa_counter = 0;
        $user->save();

        $this->guard->login($user, $request->remember());

        $request->session()->regenerate();

        return app(TwoFactorLoginResponse::class);
    }

    public function emailRecoveryCode(string $uuid, string $code)
    {
        $user = User::where('uuid', $uuid)->first();

        if ($user) {
            $user->notify(new RecoveryCode($code));
        }
    }
}
