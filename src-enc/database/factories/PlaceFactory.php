<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Organisation;
use App\Models\Place;
use App\Models\PlaceType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlaceFactory extends Factory
{
    protected $model = Place::class;

    public function definition(): array
    {
        return [
            'uuid'             => Str::uuid(),
            'organisations_id'  => Organisation::factory(),
            'locations_id'      => Location::factory(),
            'place_types_id'    => PlaceType::RESOURCED,
            'name'              => Str::random(),
            'external_id'       => Str::uuid()->toString(),
            'email'             => $this->faker->email,
            'tsm_current_state' => Place::STATE_ACTIVE,
            'metadata'          => json_encode([
                $this->faker->shuffleString => $this->faker->shuffleString
            ]),
        ];
    }

    public function resourced(): PlaceFactory
    {
        return $this->state(function () {
            return [
                'place_types_id' => PlaceType::RESOURCED,
            ];
        });
    }

    public function managed(): PlaceFactory
    {
        return $this->state(function () {
            return [
                'place_types_id' => PlaceType::MANAGED,
            ];
        });
    }

    public function open(): PlaceFactory
    {
        return $this->state(function () {
            return [
                'place_types_id' => PlaceType::OPEN,
            ];
        });
    }

}
