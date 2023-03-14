<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

class ConfirmableTwoFactorAuthenticationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code'          => 'nullable|string',
            'recovery_code' => 'nullable|string',
        ];
    }

    /**
     * Determine if the request has a valid two factor code.
     *
     * @return bool
     */
    public function hasValidCode()
    {
        $twoFactorSecret = decrypt($this->user()->two_factor_secret);

        return $this->code
            &&
            tap(
                app(TwoFactorAuthenticationProvider::class)->verify($twoFactorSecret, $this->code),
                function ($result) {
                    if ($result) {
                        $this->session()->forget('login.id');
                    }
                }
            );
    }

    /**
     * Get the valid recovery code if one exists on the request.
     *
     * @return string|null
     */
    public function validRecoveryCode()
    {
        $recoveryCodes = $this->user()->recoveryCodes();

        return $this->recovery_code
            &&
            tap(
                collect($recoveryCodes)->first(function ($code) {
                    return hash_equals($this->recovery_code, $code) ? $code : null;
                }),
                function ($code) {
                    if ($code) {
                        $this->session()->forget('login.id');
                    }
                }
            );
    }
}
