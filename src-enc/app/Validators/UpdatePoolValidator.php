<?php

namespace App\Validators;

use App\Models\Pool;
use Illuminate\Validation\Rule;

class UpdatePoolValidator extends Validator
{
    public function rules(): array
    {
        $pool = Pool::whereUuid(request()->route('uuid'))->first();

        $ruleUnique = Rule::unique('pools', 'name')
            ->where('organisations_id', auth()->user()->organisations_id);
        if ($pool) {
            $ruleUnique = $ruleUnique->ignoreModel($pool);
        }

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                $ruleUnique,
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
