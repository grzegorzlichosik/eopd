<?php

namespace App\Actions\Fortify;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param mixed $user
     * @param array $input
     * @return void
     */
    public function reset($user, array $input)
    {
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        /**
         * Save new password and reset failed_logins counter
         */
        $user->forceFill([
            'password_updated_at'=> now(),
            'password'           => Hash::make($input['password']),
            'failed_logins'      => 0,
            'email_verified_at'  => $user->email_verified_at ?? now(),
            'timezone'           => $user['timezone'] ?? $input['timezone']
        ])->save();
    }
}
