<?php

namespace App\Actions;

use Threadable\StateMachine\Components\StateMachineBaseAction;
use App\Events\Flow\FlowDraftArchiveArchived as FlowDraftArchiveArchivedEvent;

class FlowDraftArchiveArchived extends StateMachineBaseAction
{
    public function execute(): bool
    {
        event(new FlowDraftArchiveArchivedEvent($this->stateableModel, $this->user));
        return true;
    }
}
