<?php

namespace App\Actions;

use Threadable\StateMachine\Components\StateMachineBaseAction;
use App\Events\Flow\FlowInactivePublishPublished as FlowInactivePublishPublishedEvent;

class FlowInactivePublishPublished extends StateMachineBaseAction
{
    public function execute(): bool
    {
        event(new FlowInactivePublishPublishedEvent($this->stateableModel, $this->user));
        return true;
    }
}
