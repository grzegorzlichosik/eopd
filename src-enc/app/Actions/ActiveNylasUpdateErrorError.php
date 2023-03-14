<?php

namespace App\Actions;

use Threadable\StateMachine\Components\StateMachineBaseAction;
use App\Events\Place\ActiveError;

class ActiveNylasUpdateErrorError extends StateMachineBaseAction
{
    public function execute(): bool
    {
        if ($this->stateableModel) {
            event(new ActiveError($this->stateableModel, $this->user));
        }
        return true;
    }
}
