<?php

namespace App\Actions;

use Threadable\StateMachine\Components\StateMachineBaseAction;
use App\Events\Flow\FlowPublishedEditPublished as FlowPublishedEditPublishedEvent;

class FlowPublishedEditPublished extends StateMachineBaseAction
{
    public function execute(): bool
    {
        event(new FlowPublishedEditPublishedEvent($this->stateableModel, $this->user));
        return true;
    }
}
