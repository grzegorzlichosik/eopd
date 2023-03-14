<?php

namespace App\Listeners\Place;

use App\Events\Encounter\PlaceEncounterAutoReschedule;
use App\Events\Place\ActiveError;
use App\Notifications\Place\ActiveError as ActiveErrorNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ActiveErrorListener
{
    public function handle(ActiveError $event): void
    {
        event(new PlaceEncounterAutoReschedule($event->place));

        User::where('organisations_id', $event->place->organisations_id)
            ->where(fn(Builder $query) => $query->where('is_admin', 1)
                ->orWhere('is_super_admin', 1)
            )
            ->get()
            ->each(function ($user) use ($event) {
                $user->notify(new ActiveErrorNotification($event->place));
            });
    }

}
