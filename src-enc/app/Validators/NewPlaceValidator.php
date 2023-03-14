<?php

namespace App\Validators;

use App\Models\Location;
use App\Models\Place;
use App\Models\PlaceType;
use App\Rules\MustBeValidEmail;
use Illuminate\Validation\Rule;

class NewPlaceValidator extends Validator
{
    protected const RULE_MIN = 'min:3';
    protected const RULE_MAX = 'max:255';

    public function rules(): array
    {
        $authUser = auth()->user();

        return [
            'name'            => [
                'required',
                'string',
                self::RULE_MIN,
                self::RULE_MAX,
                Rule::unique('places', 'name')
                    ->where('organisations_id', $authUser->organisations_id),
            ],
            'email'           => [
                'sometimes',
                'required',
                'string',
                new MustBeValidEmail,
                self::RULE_MAX,
                Rule::unique('places', 'email')
                    ->where('organisations_id', $authUser->organisations_id),
            ],
            'description'     => [
                'required',
                'string',
                self::RULE_MIN,
                self::RULE_MAX,
            ],
            'place_type_uuid' => [
                'required',
                'uuid',
                Rule::in(PlaceType::pluck('uuid')->toArray()),
            ],
            'location_uuid'   => [
                'required',
                'uuid',
                Rule::in(Location::where('organisations_id', $authUser->organisations_id)->pluck('uuid')->toArray()),
            ],
            'status'          => [
                'required',
                Rule::in([Place::STATE_ACTIVE, Place::STATE_INACTIVE]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max'             => trans('validation.name_max', ['max' => 20]),
            'description.required' => trans('validation.description_required'),
            'description.min'      => trans('validation.description_min', ['min' => 3]),
            'description.max'      => trans('validation.description_max', ['max' => 100]),
            'location.required'    => trans('validation.location_required'),
            'type.required'        => trans('validation.type_required'),
            'status.required'      => trans('validation.status_required'),
        ];
    }
}
