<?php

namespace App\Listeners\Flow;

use App\Events\Flow\FlowPublishedArchiveArchived;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\Flow\FlowPublishedArchiveArchived as FlowPublishedArchiveArchivedNotification;

class FlowPublishedArchiveArchivedListener
{
    public function __construct(
        public FlowPublishedArchiveArchived $event
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
                $user->notify(new FlowPublishedArchiveArchivedNotification($event->flow));
            });
    }
}
