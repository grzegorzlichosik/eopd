<?php

namespace App\Events\Flow;

use App\Models\Flow;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FlowPublishedEditPublished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Flow $flow,
        public readonly User $user
    )
    {
    }

}
