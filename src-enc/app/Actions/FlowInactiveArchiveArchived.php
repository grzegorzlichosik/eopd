<?php

namespace App\Actions;

use Threadable\StateMachine\Components\StateMachineBaseAction;
use App\Events\Flow\FlowInactiveArchiveArchived as FlowInactiveArchiveArchivedEvent;

class FlowInactiveArchiveArchived extends StateMachineBaseAction
{
    public function execute(): bool
    {
        event(new FlowInactiveArchiveArchivedEvent($this->stateableModel, $this->user));
        return true;
    }
}
