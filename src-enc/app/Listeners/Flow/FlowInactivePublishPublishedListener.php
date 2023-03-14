<?php

namespace App\Listeners\Flow;

use App\Events\Flow\FlowInactivePublishPublished;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\Flow\FlowInactivePublishPublished as FlowInactivePublishPublishedNotification;

class FlowInactivePublishPublishedListener
{
    public function __construct(
        public FlowInactivePublishPublished $event
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
                $user->notify(new FlowInactivePublishPublishedNotification($event->flow));
            });
    }
}
