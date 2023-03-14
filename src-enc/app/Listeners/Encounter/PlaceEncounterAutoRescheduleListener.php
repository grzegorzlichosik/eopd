<?php

namespace App\Listeners\Encounter;

use App\Events\Encounter\PlaceEncounterAutoReschedule;
use App\Models\Encounter;

class PlaceEncounterAutoRescheduleListener
{
    public function handle(PlaceEncounterAutoReschedule $event): void
    {
        Encounter::where('scheduled_at', '>=', now())
            ->where('tsm_current_state', Encounter::STATE_SCHEDULED)
            ->where('organisations_id', $event->place->organisations_id)
            ->where('places_id', $event->place->id)
            ->get()
            ->each(
                fn($encounter) => $encounter->transit('encounter_auto_re_schedule')
            );
    }

}
