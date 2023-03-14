<?php

namespace App\Listeners\Flow;

use App\Events\Flow\FlowInitialCreateDraft;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\Flow\FlowInitialCreateDraft as FlowInitialCreateDraftNotification;

class FlowInitialCreateDraftListener
{
    public function __construct(
        public FlowInitialCreateDraft $event
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
                $user->notify(new FlowInitialCreateDraftNotification($event->flow));
            });
    }
}