<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\Location;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        $localCoordinates = $this->faker->localCoordinates;

        return [
            'uuid'             => Str::uuid(),
            'organisations_id' => Organisation::factory(),
            'name'             => $this->faker->unique()->company,
            'short_name'       => Str::random(40),
            'address_1'        => $this->faker->streetAddress,
            'address_2'        => $this->faker->streetAddress,
            'postcode'         => $this->faker->postcode,
            'city_town'        => $this->faker->city,
            'phone'            => $this->faker->phoneNumber,
            'location_lat'     => $localCoordinates['latitude'],
            'location_lon'     => $localCoordinates['longitude'],
            'timezone'         => $this->faker->timezone,
            'files_id'         => File::factory(),
        ];
    }
}
