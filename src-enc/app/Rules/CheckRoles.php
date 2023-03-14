<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckRoles implements Rule
{

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        foreach ($value as $role) {
            if (
                !in_array(
                    $role,
                    [
                        'is_super_admin',
                        'is_admin',
                        'is_agent',
                        'is_developer',
                    ]
                )
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.incorrect_roles');
    }
}
