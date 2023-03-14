<?php

namespace App\Validators;

use App\Rules\CheckLastSuperAdmin;

class DeleteUserValidator extends Validator
{
    public function rules(): array
    {
        return [
            'uuid'     => [
                new CheckLastSuperAdmin,
            ],
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
