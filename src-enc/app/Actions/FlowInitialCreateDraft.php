<?php

namespace App\Actions;

use Threadable\StateMachine\Components\StateMachineBaseAction;
use App\Events\Flow\FlowInitialCreateDraft as FlowInitialCreateDraftEvent;

class FlowInitialCreateDraft extends StateMachineBaseAction
{
    public function execute(): bool
    {
        event(new FlowInitialCreateDraftEvent($this->stateableModel, $this->user));
        return true;
    }
}
