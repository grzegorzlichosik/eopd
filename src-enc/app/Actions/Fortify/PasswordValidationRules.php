<?php

namespace App\Actions\Fortify;

use App\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array
     */
    protected function passwordRules(): array
    {
        return [
            'required',
            'string',
            (new Password)->length(12)
                ->requireUppercase()
                ->requireNumeric()
                ->requireLowercase(),
            'confirmed'
        ];
    }
}
