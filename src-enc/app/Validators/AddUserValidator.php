<?php

namespace App\Validators;

class AddUserValidator extends Validator
{
    public function rules(): array
    {
        return [
            'selected_users'        => 'required|array',
            'selected_users.*.name' => 'string',
            'selected_users.*.uuid' => 'uuid',
        ];
    }

    public function messages(): array
    {
        return [
            'selected_users.required' => trans('validation.selected_users_required'),
        ];
    }
}
