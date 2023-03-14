<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class CheckAuthUserLastSuperAdmin implements Rule
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
        $authUser = auth()->user();

        $user = User::where('uuid', request()->route('uuid'))
            ->where('organisations_id', $authUser->organisations_id)
            ->first();

        $countOfSuperAdmins = User::where('organisations_id', $authUser->organisations_id)
            ->superAdministrator()
            ->count();

        if (
            $user->id === $authUser->id
            &&
            !in_array('is_super_admin', $value)
            &&
            $user->is_super_admin
            &&
            $countOfSuperAdmins < 2
        ) {
            return false;
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
        return trans('validation.last_super_admin_remove_role');
    }
}
