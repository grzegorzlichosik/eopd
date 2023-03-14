<?php

namespace App\Validators;


use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdateLocationValidator extends Validator
{
    protected const RULE_MIN = 'min:3';
    protected const RULE_MAX = 'max:255';

    public function rules(): array
    {
        $authUser = auth()->user();
        $location = Location::whereUuid(request()->route('uuid'))->first();
        $nameUnique = Rule::unique('locations', 'name')
            ->where('organisations_id', $authUser->organisations_id);
        $shortNameUnique = Rule::unique('locations', 'short_name')
            ->where('organisations_id', $authUser->organisations_id);
        if ($location) {
            $nameUnique = $nameUnique->ignoreModel($location);
            $shortNameUnique = $shortNameUnique->ignoreModel($location);
        }

        return [
            'name'       => [
                'required',
                'string',
                self::RULE_MIN,
                self::RULE_MAX,
                $nameUnique
            ],
            'short_name' => [
                'required',
                'string',
                self::RULE_MIN,
                'max:40',
                $shortNameUnique
            ],

            'address' => [
                'required',
                'string',
                self::RULE_MIN,
                self::RULE_MAX,
            ],
            'postcode'  => [
                'nullable',
            ],
            'city_town' => [
                'nullable',
                'string',
                self::RULE_MIN,
                self::RULE_MAX,
            ],
            'phone'     => [
                'nullable',
                'phone:' . request()->get('country_code'),
            ],
            'timezone'  => [
                'required',
            ],
        ];
    }

    public function messages(): array
    {
         return [
            'short_name.required' => trans('validation.short_name_required'),
            'short_name.string'   => trans('validation.short_name_string'),
            'short_name.min'      => trans('validation.short_name_min', ['min' => 3]),
            'short_name.max'      => trans('validation.short_name_max', ['max' => 40]),
            'city_town.string'    => trans('validation.city_town_string'),
            'city_town.min'       => trans('validation.city_town_min', ['min' => 3]),
            'city_town.max'       => trans('validation.city_town_max', ['max' => 255]),
            'timezone.required'   => trans('validation.timezone_required'),
        ];
    }
}
