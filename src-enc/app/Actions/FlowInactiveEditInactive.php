<?php

namespace App\Actions;

use Threadable\StateMachine\Components\StateMachineBaseAction;
use App\Events\Flow\FlowInactiveEditInactive as FlowInactiveEditInactiveEvent;

class FlowInactiveEditInactive extends StateMachineBaseAction
{
    public function execute(): bool
    {
        event(new FlowInactiveEditInactiveEvent($this->stateableModel, $this->user));
        return true;
    }
}
