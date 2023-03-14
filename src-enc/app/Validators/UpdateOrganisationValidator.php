<?php

namespace App\Validators;

use App\Models\Organisation;
use Illuminate\Validation\Rule;

class UpdateOrganisationValidator extends Validator
{
    public function rules(): array
    {
        $ruleUnique = Rule::unique('organisations', 'name')
            ->ignoreModel(auth()->user()->organisation);

        return [
            'name'         => [
                'required',
                'string',
                'min:3',
                'max:255',
                $ruleUnique
            ],
            'phone_number' => [
                'required',
                'phone:' . request()->get('country_code'),
            ],

            'file' => [
                'nullable',
                'base64mimes:jpg,png,jpeg',
                'base64max:2048'
            ],

            'color' => [
                'nullable'
            ]

        ];
    }

    public function messages(): array
    {
        return [
            'name.min'    => trans('validation.name_min'),
            'name.unique' => trans('validation.organisation_exists'),
            'file.mimes'  => trans('validation.image_type'),
        ];
    }
}
