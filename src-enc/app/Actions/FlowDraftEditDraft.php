<?php

namespace App\Actions;

use Threadable\StateMachine\Components\StateMachineBaseAction;
use App\Events\Flow\FlowDraftEditDraft as FlowDraftEditDraftEvent;

class FlowDraftEditDraft extends StateMachineBaseAction
{
    public function execute(): bool
    {
        event(new FlowDraftEditDraftEvent($this->stateableModel, $this->user));
        return true;
    }
}
