<?php

namespace App\Events\Place;

use App\Models\Place;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActiveError
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Place $place,
        public readonly ?User $user,
    )
    {
    }
}
