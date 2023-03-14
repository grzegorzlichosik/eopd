<?php

namespace App\Listeners\Place;

use App\Events\Encounter\PlaceEncounterAutoReschedule;
use App\Events\Place\ActiveDeactivated;
use App\Notifications\Place\ActiveDeactivated as ActiveDeactivatedNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ActiveDeactivatedListener
{
    public function handle(ActiveDeactivated $event): void
    {
        event(new PlaceEncounterAutoReschedule($event->place));

        User::where('organisations_id', $event->place->organisations_id)
            ->where(fn(Builder $query) => $query->where('is_admin', 1)
                ->orWhere('is_super_admin', 1)
            )
            ->get()
            ->each(function ($user) use ($event) {
                $user->notify(new ActiveDeactivatedNotification($event->place));
            });
    }

}
