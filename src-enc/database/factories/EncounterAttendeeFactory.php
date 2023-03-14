<?php

namespace Database\Factories;

use App\Models\Encounter;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class EncounterAttendeeFactory extends Factory
{
    public function definition()
    {
        return [
            'encounters_id' => Encounter::factory(),
            'name'          => $this->faker->name,
            'email'         => $this->faker->unique()->email,
            'is_original'   => Arr::random([0, 1]),
            'is_accepted'   => Arr::random([0, 1]),
        ];
    }

    public function original(): EncounterAttendeeFactory
    {
        return $this->state(function () {
            return [
                'is_original' => 1,
            ];
        });
    }

    public function accepted(): EncounterAttendeeFactory
    {
        return $this->state(function () {
            return [
                'is_accepted' => 1,
            ];
        });
    }
}
