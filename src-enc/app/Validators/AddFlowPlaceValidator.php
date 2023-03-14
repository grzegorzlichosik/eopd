<?php

namespace App\Validators;

class AddFlowPlaceValidator extends Validator
{
    public function rules(): array
    {
        return [
            'selected_flows'        => 'required|array',
            'selected_flows.*.name' => 'string',
            'selected_flows.*.uuid' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'selected_flows.required' => trans('validation.selected_flows_required'),
        ];
    }
}
