<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\ChannelType;
use App\Models\Encounter;
use App\Models\Flow;
use App\Models\Organisation;
use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EncounterFactory extends Factory
{
    public function definition()
    {
        return [
            'organisations_id'  => Organisation::factory(),
            'flows_id'          => Flow::factory(),
            'agent_id'          => User::factory(),
            'places_id'         => Place::factory(),
            'external_id'       => Str::uuid()->toString(),
            'channels_id'       => Channel::factory(),
            'channel_types_id'  => ChannelType::F2F,
            'tsm_current_state' => Encounter::STATE_SCHEDULED,
            'metadata'          => null,
            'scheduled_at'      => now(),
            'ends_at'           => now()->addMinutes(30),
        ];
    }
}
