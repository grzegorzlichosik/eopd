<?php

namespace App\Events\Encounter;

use App\Models\Place;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlaceEncounterAutoReschedule
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Place $place
    )
    {
    }
}
