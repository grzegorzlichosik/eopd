<?php

namespace App\Listeners\Place;

use App\Events\Encounter\PlaceEncounterAutoReschedule;
use App\Events\Place\ActiveArchived;
use App\Notifications\Place\ActiveArchived as ActiveArchivedNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ActiveArchivedListener
{
    public function handle(ActiveArchived $event): void
    {
        event(new PlaceEncounterAutoReschedule($event->place));

        User::where('organisations_id', $event->place->organisations_id)
            ->where(fn(Builder $query) => $query->where('is_admin', 1)
                ->orWhere('is_super_admin', 1)
            )
            ->get()
            ->each(function ($user) use ($event) {
                $user->notify(new ActiveArchivedNotification($event->place));
            });
    }

}
