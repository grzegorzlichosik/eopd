<?php

namespace App\Actions;

use Threadable\StateMachine\Components\StateMachineBaseAction;
use App\Events\Place\ActiveDeactivated;

class ActiveDeactivateInactive extends StateMachineBaseAction
{
    public function execute(): bool
    {
        event(new ActiveDeactivated($this->stateableModel, $this->user));
        return true;
    }
}
