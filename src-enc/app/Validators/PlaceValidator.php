<?php

namespace App\Validators;

use App\Models\Location;
use App\Models\Place;
use App\Models\PlaceType;
use App\Rules\MustBeValidEmail;
use Illuminate\Validation\Rule;

class PlaceValidator extends Validator
{
    protected const RULE_MIN = 'min:3';
    protected const RULE_MAX = 'max:255';

    public function rules(): array
    {
        $authUser = auth()->user();
        $uuid = request()->route('uuid');

        return [
            'name'            => [
                'sometimes',
                'required',
                'string',
                self::RULE_MIN,
                self::RULE_MAX,
                Rule::unique('places', 'name')
                    ->where('organisations_id', $authUser->organisations_id)
                    ->ignore($uuid, 'uuid'),
            ],
            'email'           => [
                'sometimes',
                'required',
                'string',
                new MustBeValidEmail,
                self::RULE_MAX,
                Rule::unique('places', 'email')
                    ->where('organisations_id', $authUser->organisations_id)
                    ->ignore($uuid, 'uuid'),
            ],
            'description'     => [
                'sometimes',
                'required',
                'string',
                self::RULE_MIN,
                self::RULE_MAX,
            ],
            'place_type_uuid' => [
                'sometimes',
                'required',
                'uuid',
                Rule::in(PlaceType::pluck('uuid')->toArray()),
            ],
            'location_uuid'   => [
                'sometimes',
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
            'name.required'   => trans('validation.name_required'),
            'name.string'     => trans('validation.name_string'),
            'name.min'        => trans('validation.name_min', ['min' => 3]),
            'name.max'        => trans('validation.name_max', ['max' => 255]),
            'name.unique'     => trans('validation.location_unique'),
            'description.min' => trans('validation.description_min', ['min' => 3]),
        ];
    }
}
