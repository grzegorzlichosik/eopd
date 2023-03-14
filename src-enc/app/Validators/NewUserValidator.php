<?php

namespace App\Validators;


use App\Rules\CheckRoles;
use App\Rules\MustBeValidEmail;

class NewUserValidator extends Validator
{
    protected const RULE_MIN = 'min:3';
    protected const RULE_MAX = 'max:255';

    public function rules(): array
    {
        return [
            'name'  => [
                'required',
                'string',
                self::RULE_MIN,
                self::RULE_MAX,
            ],
            'email' => [
                'required',
                'string',
                self::RULE_MAX,
                new MustBeValidEmail,
                'unique:users'
            ],
            'roles' => [
                'required',
                'array',
                new CheckRoles
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => trans('validation.name_required'),
            'name.string'    => trans('validation.name_string'),
            'name.min'       => trans('validation.name_min', ['min' => 3]),
            'name.max'       => trans('validation.name_max', ['max' => 255]),
            'email.required' => trans('validation.email_required'),
            'email.unique'   => trans('validation.email_unique'),
            'email.max'      => trans('validation.email_max', ['max' => 255]),
            'roles.required' => trans('validation.please_select_at_one_roles'),
            'roles.array'    => trans('validation.roles_array'),
        ];
    }
}
