<?php

namespace App\Validators;

use Illuminate\Validation\Rule;

class CreatePoolValidator extends Validator
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('pools', 'name')->where('organisations_id', auth()->user()->organisations_id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => trans('validation.name_required'),
            'name.string'   => trans('validation.name_string'),
            'name.min'      => trans('validation.name_min', ['min' => 3]),
            'name.max'      => trans('validation.name_max', ['max' => 255]),
            'name.unique'   => trans('validation.pool_exists'),
        ];
    }
}
