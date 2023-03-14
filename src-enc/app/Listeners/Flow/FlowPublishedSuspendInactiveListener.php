<?php

namespace App\Listeners\Flow;

use App\Events\Flow\FlowPublishedSuspendInactive;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\Flow\FlowPublishedSuspendInactive as FlowPublishedSuspendInactiveNotification;

class FlowPublishedSuspendInactiveListener
{
    public function __construct(
        public FlowPublishedSuspendInactive $event
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
                $user->notify(new FlowPublishedSuspendInactiveNotification($event->flow));
            });
    }
}
