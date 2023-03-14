<?php

namespace App\Listeners\Flow;

use App\Events\Flow\FlowInactiveEditInactive;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\Flow\FlowInactiveEditInactive as FlowInactiveEditInactiveNotification;

class FlowInactiveEditInactiveListener
{
    public function __construct(
        public FlowInactiveEditInactive $event
    )
    {
    }

    public function handle($event)
    {
        User::where('organisations_id', $event->flow->organisations_id)
            ->where(fn(Builder $query) => $query->where('is_admin', 1)
                ->orWhere('is_super_admin', 1)
            )
            ->get()
            ->each(function ($user) use ($event) {
                $user->notify(new FlowInactiveEditInactiveNotification($event->flow));
            });
    }
}
