<?php

namespace App\Actions;

use Threadable\StateMachine\Components\StateMachineBaseAction;
use App\Events\Flow\FlowDraftPublishPublished as FlowDraftPublishPublishedEvent;

class FlowDraftPublishPublished extends StateMachineBaseAction
{
    public function execute(): bool
    {
        event(new FlowDraftPublishPublishedEvent($this->stateableModel, $this->user));
        return true;
    }
}
