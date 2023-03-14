<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class CheckLastSuperAdmin implements Rule
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

        if ($user->is_super_admin) {
            return User::where('organisations_id', auth()->user()->organisations_id)
                    ->where('uuid', '<>', $value)
                    ->superAdministrator()
                    ->count() >= 1;
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
        return trans('validation.last_super_admin_cannot_delete');
    }
}
