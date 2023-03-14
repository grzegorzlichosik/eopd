<?php

namespace App\Listeners\Flow;

use App\Events\Flow\FlowInactiveArchiveArchived;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\Flow\FlowInactiveArchiveArchived as FlowInactiveArchiveArchivedNotification;

class FlowInactiveArchiveArchivedListener
{
    public function __construct(
        public FlowInactiveArchiveArchived $event
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
                $user->notify(new FlowInactiveArchiveArchivedNotification($event->flow));
            });
    }
}
