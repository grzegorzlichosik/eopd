<?php

namespace App\Validators;

use App\Models\Flow;
use Illuminate\Validation\Rule;

class CreateFlowValidator extends Validator
{
    public function rules(): array
    {
        return [
            'name'      => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('flows', 'name')->where('organisations_id', auth()->user()->organisations_id),
            ],
            'objective' => [
                'required',
                'string',
                'min:3',
                'max:255'
            ],
            'channels'  => 'required|array'
        ];
    }

    public function messages(): array
    {
        return [
            'objective.required' => trans('validation.objective_required'),
            'objective.string'   => trans('validation.objective_string'),
            'objective.min'      => trans('validation.objective_min', ['min' => 3]),
            'objective.max'      => trans('validation.objective_max', ['max' => 255]),
            'channels.required'  => trans('validation.channels_required_array_keys', ['max' => 255]),
        ];
    }
}

