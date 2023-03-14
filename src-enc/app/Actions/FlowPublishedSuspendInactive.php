<?php

namespace App\Actions;

use Threadable\StateMachine\Components\StateMachineBaseAction;
use App\Events\Flow\FlowPublishedSuspendInactive as FlowPublishedSuspendInactiveEvent;

class FlowPublishedSuspendInactive extends StateMachineBaseAction
{
    public function execute(): bool
    {
        event(new FlowPublishedSuspendInactiveEvent($this->stateableModel, $this->user));
        return true;
    }
}
