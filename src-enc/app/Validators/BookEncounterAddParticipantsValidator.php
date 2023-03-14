<?php

namespace App\Validators;

use App\Rules\MustBeValidEmail;

class BookEncounterAddParticipantsValidator extends Validator
{
    /**
     * @codeCoverageIgnore
     */
    public function rules(): array
    {

        return [
            'participants'            => 'array|min:1',
            'participants.*.name'     => 'required|string|max:255',
            'participants.*.email' => [
                'required',
                'string',
                'max:255',
                new MustBeValidEmail,
            ],
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function messages(): array
    {
        return [
            'participants.*.name.required'  => trans('validation.name_required'),
            'participants.*.name.string'  => trans('validation.name_string'),
            'participants.*.name.max'     => trans('validation.name_max', ['max' => 255]),
            'participants.*.email.required'     => trans('validation.email_required'),
            'participants.*.email.string' => trans('validation.email_string'),
            'participants.*.email.max'    => trans('validation.email_max', ['max' => 255]),
        ];
    }
}
