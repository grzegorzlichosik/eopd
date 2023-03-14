<?php

namespace App\Validators;

use App\Models\Channel;
use App\Models\ChannelType;
use App\Rules\MustBeValidEmail;

class BookEncounterValidator extends Validator
{
    /**
     * @codeCoverageIgnore
     */
    public function rules(): array
    {

        $channel = Channel::whereUuid(request()->get('channel'))->first();
        $phoneNumberRule = $channel?->channel_types_id === ChannelType::PHONE
            ? 'required'
            : 'nullable';

        return [
            'attendee_name'  => [
                'required',
                'string',
                'max:255',
            ],
            'attendee_email' => [
                'required',
                new MustBeValidEmail,
                'max:255',
            ],
            'phone_number'   => [
                $phoneNumberRule,
                'phone:' . request()->get('country_code'),
            ],
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function messages(): array
    {
        return [
            'attendee_name.required'  => trans('validation.name_required'),
            'attendee_name.string'    => trans('validation.name_string'),
            'attendee_name.min'       => trans('validation.name_min', ['min' => 3]),
            'attendee_name.max'       => trans('validation.name_max', ['max' => 255]),
            'attendee_email.required' => trans('validation.email_required'),
            'attendee_email.string'   => trans('validation.email_string'),
            'attendee_email.max'      => trans('validation.email_max', ['max' => 255]),
            'phone_number.required'   => trans('validation.phone_required'),
        ];
    }
}
