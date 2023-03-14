<?php

namespace App\Actions;

use Threadable\StateMachine\Components\StateMachineBaseAction;
use App\Events\Place\ActiveArchived;

class ActiveArchiveArchived extends StateMachineBaseAction
{
    public function execute(): bool
    {
        event(new ActiveArchived($this->stateableModel, $this->user));
        return true;
    }
}
