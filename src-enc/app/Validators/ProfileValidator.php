<?php

namespace App\Validators;

use Illuminate\Validation\Rule;

class ProfileValidator extends Validator
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'timezone' => [
                'required',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.min'          => trans('validation.name_min', ['min' => 3]),
            'timezone.required' => trans('validation.timezone_required'),
        ];
    }
}
