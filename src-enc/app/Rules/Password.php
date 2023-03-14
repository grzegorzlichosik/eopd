<?php

namespace App\Rules;

use Laravel\Fortify\Rules\Password as FortifyPassword;
use Illuminate\Support\Str;

class Password extends FortifyPassword
{
    protected $length = 12;

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    protected $requireLowercase = false;

    public function passes($attribute, $value): bool
    {
        $value = is_scalar($value) ? (string)$value : '';

        $uppercaseRegex = '/^(.*[A-Z]){2,}.*$/';
        $numericRegex = '/^(.*\d){2,}.*$/';
        $lowercaseRegex = '/^(.*[a-z]){2,}.*$/';

        if (
            ($this->requireUppercase && !preg_match($uppercaseRegex, $value))
            ||
            ($this->requireNumeric && !preg_match($numericRegex, $value))
            ||
            ($this->requireLowercase && !preg_match($lowercaseRegex, $value))

        ) {
            return false;
        }

        return Str::length($value) >= $this->length;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.password_rule', ['length' => $this->length]);
    }

    public function requireLowercase(): Password
    {
        $this->requireLowercase = true;

        return $this;
    }

}
