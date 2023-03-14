<?php

namespace App\Actions;

use Threadable\StateMachine\Components\StateMachineBaseAction;
use App\Events\Flow\FlowPublishedArchiveArchived as FlowPublishedArchiveArchivedEvent;

class FlowPublishedArchiveArchived extends StateMachineBaseAction
{
    public function execute(): bool
    {
        event(new FlowPublishedArchiveArchivedEvent($this->stateableModel, $this->user));
        return true;
    }
}
