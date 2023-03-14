<?php

namespace App\Validators;


use App\Rules\MustBeValidEmail;

class EmailValidator extends Validator
{

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                new MustBeValidEmail,
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
