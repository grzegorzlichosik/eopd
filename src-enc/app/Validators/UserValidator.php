<?php

namespace App\Validators;

use App\Rules\CheckAuthUserLastSuperAdmin;
use App\Rules\CheckIfUserVerified;
use App\Rules\CheckRoles;
use App\Rules\MustBeValidEmail;

class UserValidator extends Validator
{
    public function rules(): array
    {
        $uuid = request()->route('uuid');

        return [
            'name'     => [
                'sometimes',
                'string',
                'max:255',
            ],
            'email'    => [
                'sometimes',
                'string',
                new MustBeValidEmail,
                'max:255',
                'unique:users,email,' . $uuid . ',uuid',
                new CheckIfUserVerified,
            ],
            'roles' => [
                'required',
                'array',
                new CheckRoles,
                new CheckAuthUserLastSuperAdmin,
            ],
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
