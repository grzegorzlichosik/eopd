<?php

namespace App\Validators;

class AddAgentValidator extends Validator
{
    public function rules(): array
    {
        return [
            'selected_agents'        => 'required|array',
            'selected_agents.*.name' => 'string',
            'selected_agents.*.uuid' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'selected_agents.required' => trans('validation.selected_agents_required'),
        ];
    }
}
